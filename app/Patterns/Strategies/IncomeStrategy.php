<?php

namespace App\Patterns\Strategies;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Movement;

class IncomeStrategy implements MovementStrategyInterface
{
    public function execute(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            $account = $user->accounts()->findOrFail($data['account_id']);
            $account->balance += $data['amount'];
            $account->save();
        });
    }

    public function revert(Movement $movement): void
{
    DB::transaction(function () use ($movement) {
        // Un ingreso se revierte restando el monto de la cuenta
        $account = $movement->account;
        $account->balance -= $movement->amount;
        $account->save();
    });
}
}
