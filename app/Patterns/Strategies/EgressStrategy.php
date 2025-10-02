<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;

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

            // Finalmente, creamos el registro del movimiento
            $user->movements()->create($data);
        });
    }
}
