<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;

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

            // 4. Crear el registro del movimiento de transferencia
            $user->movements()->create($data);
        });
    }
}
