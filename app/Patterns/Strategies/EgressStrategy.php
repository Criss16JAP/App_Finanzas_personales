<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Movement;

class EgressStrategy implements MovementStrategyInterface
{
    public function execute(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            $account = $user->accounts()->findOrFail($data['account_id']);

            // LÓGICA CONDICIONAL BASADA EN EL TIPO DE CUENTA
            if ($account->type === 'credit_card') {
                // Para tarjetas de crédito, un gasto AUMENTA la deuda (el balance)
                $account->balance += $data['amount'];
            } else {
                // Para otras cuentas (banco, efectivo), un gasto DISMINUYE el saldo
                if ($account->balance < $data['amount']) {
                    throw new \Exception('Saldo insuficiente en la cuenta.');
                }
                $account->balance -= $data['amount'];
            }

            // Guardamos los cambios en la cuenta
            $account->save();
        });
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
