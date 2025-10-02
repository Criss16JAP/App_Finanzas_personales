<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Movement;

class TransferStrategy implements MovementStrategyInterface
{
    public function execute(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            $sourceAccount = $user->accounts()->findOrFail($data['account_id']);
            $destinationAccount = $user->accounts()->findOrFail($data['related_account_id']);

            if ($sourceAccount->id === $destinationAccount->id) {
                throw new \Exception('La cuenta de origen y destino no pueden ser la misma.');
            }

            if ($sourceAccount->balance < $data['amount']) {
                throw new \Exception('Saldo insuficiente en la cuenta de origen.');
            }

            // 1. Lógica para la cuenta de ORIGEN (siempre se resta)
            $sourceAccount->balance -= $data['amount'];

            // 2. LÓGICA CONDICIONAL para la cuenta de DESTINO
            $debtAccountTypes = ['credit_card', 'loan'];
            if (in_array($destinationAccount->type, $debtAccountTypes)) {
                // Si el destino es una deuda, un pago DISMINUYE el balance (la deuda)
                $destinationAccount->balance -= $data['amount'];
            } else {
                // Para otras cuentas (banco, efectivo), una transferencia AUMENTA el saldo
                $destinationAccount->balance += $data['amount'];
            }

            // 3. Guardar los cambios en ambas cuentas
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
