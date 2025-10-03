<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(User $user)
    {
        $now = now();

        $assetAccountTypes = ['bank', 'cash'];
        $totalBalance = $user->accounts()->whereIn('type', $assetAccountTypes)->sum('balance');

        $monthlyIncome = $user->movements()
            ->where('type', 'income')
            ->whereYear('movement_date', $now->year)
            ->whereMonth('movement_date', $now->month)
            ->sum('amount');

        $monthlyExpenses = $user->movements()
            ->where('type', 'egress')
            ->whereYear('movement_date', $now->year)
            ->whereMonth('movement_date', $now->month)
            ->sum('amount');

        $totalLoaned = $user->loans()
            ->where('status', 'pending')
            ->sum(DB::raw('total_amount - paid_amount'));

        $totalCreditCardDebt = $user->accounts()->where('type', 'credit_card')->sum('balance');

        // --- LÍNEA CORREGIDA ---
        // Ahora suma el 'current_balance' de la nueva tabla 'credits'
        $totalCreditDebt = $user->credits()->where('status', 'active')->sum('current_balance');

        return compact(
            'totalBalance',
            'monthlyIncome',
            'monthlyExpenses',
            'totalLoaned',
            'totalCreditCardDebt',
            'totalCreditDebt' // <-- Nombre de variable actualizado
        );
    }
}
