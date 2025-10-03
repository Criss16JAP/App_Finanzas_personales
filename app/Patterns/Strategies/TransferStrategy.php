<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Movement;
use App\Models\CreditCard;

class TransferStrategy implements MovementStrategyInterface
{
    public function execute(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            $sourceAccount = $user->accounts()->findOrFail($data['account_id']);
            $destinationAccount = $user->accounts()->findOrFail($data['related_account_id']);

            // ... (validaciones de saldo y de no ser la misma cuenta) ...

            // 1. Lógica para la cuenta de ORIGEN (siempre se resta)
            $sourceAccount->balance -= $data['amount'];

            // 2. Lógica para la cuenta de DESTINO
            $debtAccountTypes = ['credit_card', 'loan'];
            if (in_array($destinationAccount->type, $debtAccountTypes)) {
                // Si el destino es una deuda, un pago DISMINUYE el balance
                $destinationAccount->balance -= $data['amount'];

                // --- SINCRONIZACIÓN AÑADIDA ---
                // Si es una tarjeta, también actualizamos su tabla especializada
                if ($destinationAccount->type === 'credit_card') {
                    $creditCard = CreditCard::where('name', $destinationAccount->name)
                        ->where('user_id', $user->id)
                        ->first();
                    if ($creditCard) {
                        $creditCard->current_debt -= $data['amount'];
                        $creditCard->save();
                    }
                }

            } else {
                // Para otras cuentas, una transferencia AUMENTA el saldo
                $destinationAccount->balance += $data['amount'];
            }

            $sourceAccount->save();
            $destinationAccount->save();
        });
    }

    public function revert(Movement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $sourceAccount = $movement->account;
            $destinationAccount = $movement->relatedAccount;

            // 1. Devolver el dinero a la cuenta de ORIGEN
            $sourceAccount->balance += $movement->amount;

            // 2. Retirar el dinero de la cuenta de DESTINO (lógica inversa de execute)
            $debtAccountTypes = ['credit_card', 'loan'];
            if (in_array($destinationAccount->type, $debtAccountTypes)) {
                // Revertir un pago a una deuda AUMENTA la deuda
                $destinationAccount->balance += $movement->amount;
            } else {
                // Revertir una transferencia a una cuenta normal RESTA el saldo
                $destinationAccount->balance -= $movement->amount;
            }

            $sourceAccount->save();
            $destinationAccount->save();
        });
    }
}
