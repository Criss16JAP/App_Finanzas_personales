<?php

namespace App\Services;

use App\Models\Account;
use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditCardService
{
    public function __construct()
    {
    }

    public function getCreditCardsForUser(User $user)
    {
        return $user->creditCards()->get();
    }

    public function createCreditCard(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            $creditCard = $user->creditCards()->create($data);
            $user->accounts()->create([
                'name' => $creditCard->name,
                'type' => 'credit_card',
                'balance' => 0,
            ]);
        });
    }

    public function addPurchase(CreditCard $card, array $data, User $user): void
    {
        DB::transaction(function () use ($card, $data, $user) {
            // 1. Validar el límite de crédito
            if (($card->current_debt + $data['purchase_amount']) > $card->credit_limit) {
                throw new \Exception('La compra excede el límite de crédito disponible.');
            }

            // 2. Crear el registro de la compra en su tabla
            $card->purchases()->create([
                'description' => $data['description'],
                'purchase_amount' => $data['purchase_amount'],
                'installments' => $data['installments'],
                'remaining_balance' => $data['purchase_amount'],
                'purchase_date' => $data['purchase_date'],
            ]);

            // 3. Actualizar la deuda total de la tarjeta
            $card->current_debt += $data['purchase_amount'];
            $card->save();

            // 4. Sincronizar el saldo de la cuenta ancla
            $account = Account::where('name', $card->name)
                ->where('user_id', $user->id)
                ->where('type', 'credit_card')
                ->firstOrFail();
            $account->balance = $card->current_debt;
            $account->save();

            // 5. ¡IMPORTANTE! Ya no se llama a MovementService desde aquí.
        });
    }
}
