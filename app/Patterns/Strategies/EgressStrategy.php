<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Movement;
use App\Models\Account;
use App\Services\CreditCardService;

class EgressStrategy implements MovementStrategyInterface
{
    public function __construct(private CreditCardService $creditCardService)
    {
    }

    public function execute(array $data, User $user): void
    {
        $account = $user->accounts()->findOrFail($data['account_id']);

        if ($account->type === 'credit_card') {
            $creditCard = $user->creditCards()->where('name', $account->name)->firstOrFail();

            $purchaseData = [
                'description' => $data['description'] ?? 'Gasto general',
                'purchase_amount' => $data['amount'],
                'installments' => $data['installments'] ?? 1,
                'purchase_date' => $data['movement_date'],
                'category_id' => $data['category_id'],
            ];

            // Se delega la lógica al servicio, que ahora es seguro llamar
            $this->creditCardService->addPurchase($creditCard, $purchaseData, $user);

        } else {
            // Lógica para cuentas normales
            DB::transaction(function () use ($account, $data) {
                if ($account->balance < $data['amount']) {
                    throw new \Exception('Saldo insuficiente en la cuenta.');
                }
                $account->balance -= $data['amount'];
                $account->save();
            });
        }
    }

    public function revert(Movement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $account = $movement->account;
            // La lógica es la inversa de 'execute'
            if ($account->type === 'credit_card') {
                // Revertir un gasto en tarjeta de crédito DISMINUYE la deuda
                $account->balance -= $movement->amount;
            } else {
                // Revertir un gasto normal DEVUELVE el dinero a la cuenta
                $account->balance += $movement->amount;
            }
            $account->save();
        });
    }
}
