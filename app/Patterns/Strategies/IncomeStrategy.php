<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncomeStrategy implements MovementStrategyInterface
{
    public function execute(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            // 1. Crear el movimiento
            $user->movements()->create($data);

            // 2. Actualizar el saldo de la cuenta de destino
            $account = $user->accounts()->findOrFail($data['account_id']);
            $account->balance += $data['amount'];
            $account->save();
        });
    }
}
