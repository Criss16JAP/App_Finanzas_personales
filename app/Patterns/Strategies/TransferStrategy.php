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

            // 1. Crear el movimiento de transferencia
            $user->movements()->create($data);

            // 2. Actualizar saldos
            $sourceAccount->balance -= $data['amount'];
            $destinationAccount->balance += $data['amount'];
            $sourceAccount->save();
            $destinationAccount->save();
        });
    }
}
