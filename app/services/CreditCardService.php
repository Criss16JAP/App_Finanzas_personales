<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\CreditCard;
use App\Models\Account;

class CreditCardService
{
    public function getCreditCardsForUser(User $user)
    {
        return $user->creditCards()->get();
    }

    public function __construct(private MovementService $movementService)
    {
    }

    public function createCreditCard(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            // 1. Crear la Tarjeta de Crédito en su tabla dedicada
            $creditCard = $user->creditCards()->create($data);

            // 2. Crear una "Cuenta" ancla para asociarla a los movimientos
            // Esto permite que el sistema de movimientos la reconozca
            $user->accounts()->create([
                'name' => $creditCard->name,
                'type' => 'credit_card',
                // El balance aquí representa la deuda, inicia en 0
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

            // 2. Crear el registro de la compra
            $purchase = $card->purchases()->create([
                'description' => $data['description'],
                'purchase_amount' => $data['purchase_amount'],
                'installments' => $data['installments'],
                'remaining_balance' => $data['purchase_amount'],
                'purchase_date' => $data['purchase_date'],
            ]);

            // 3. Actualizar la deuda total de la tarjeta
            $card->current_debt += $purchase->purchase_amount;
            $card->save();

            // 4. Encontrar la "cuenta ancla" para registrar el movimiento
            $account = Account::where('name', $card->name)->where('user_id', $user->id)->firstOrFail();

            // 5. Crear el movimiento de GASTO
            $this->movementService->createMovement([
                'type' => 'egress',
                'amount' => $purchase->purchase_amount,
                'account_id' => $account->id,
                'category_id' => $data['category_id'], // Necesitaremos una categoría para la compra
                'description' => $purchase->description,
                'movement_date' => $purchase->purchase_date,
            ], $user);
        });
    }
}
