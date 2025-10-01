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

            // 1. Validar que hay saldo suficiente
            if ($account->balance < $data['amount']) {
                throw new \Exception('Saldo insuficiente en la cuenta.');
            }

            // 2. Crear el movimiento
            $user->movements()->create($data);

            // 3. Actualizar el saldo de la cuenta de origen
            $account->balance -= $data['amount'];
            $account->save();
        });
    }
}
