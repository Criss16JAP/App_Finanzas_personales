<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(User $user)
{
    $now = now();

    // Saldo total de todas las cuentas (activos)
    $assetAccountTypes = ['bank', 'cash'];
    $totalBalance = $user->accounts()->whereIn('type', $assetAccountTypes)->sum('balance');

    // Ingresos del mes actual
    $monthlyIncome = $user->movements()
        ->where('type', 'income')
        ->whereYear('movement_date', $now->year)
        ->whereMonth('movement_date', $now->month)
        ->sum('amount');

    // Gastos del mes actual
    $monthlyExpenses = $user->movements()
        ->where('type', 'egress')
        ->whereYear('movement_date', $now->year)
        ->whereMonth('movement_date', $now->month)
        ->sum('amount');

    // Saldo total pendiente de préstamos que has hecho (cuentas por cobrar)
    $totalLoaned = $user->loans()
        ->where('status', 'pending')
        ->sum(DB::raw('total_amount - paid_amount'));

    // --- NUEVAS CONSULTAS ---
    // Deuda total en tarjetas de crédito
    $totalCreditCardDebt = $user->accounts()->where('type', 'credit_card')->sum('balance');

    // Deuda total en créditos que has adquirido
    $totalLoanDebt = $user->accounts()->where('type', 'loan')->sum('balance');

    return compact(
        'totalBalance',
        'monthlyIncome',
        'monthlyExpenses',
        'totalLoaned',
        'totalCreditCardDebt',
        'totalLoanDebt'
    );
}
}
