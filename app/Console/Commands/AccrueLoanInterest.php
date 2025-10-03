<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccrueLoanInterest extends Command
{
    protected $signature = 'loans:accrue-interest';
    protected $description = 'Calculates and adds monthly interest to active loans receivable.';

    public function handle()
    {
        $this->info('Starting to accrue monthly loan interest...');
        $today = now()->startOfDay();

        $activeLoans = Loan::where('status', 'pending')->get();

        foreach ($activeLoans as $loan) {
            $chargeDate = now()->setDay($loan->payment_day_of_month)->subDays(5)->startOfDay();

            if ($today->isSameDay($chargeDate) && (is_null($loan->last_interest_accrued_on) || $loan->last_interest_accrued_on < $chargeDate)) {

                DB::transaction(function () use ($loan, $today) {
                    // Calcular interés sobre el capital pendiente
                    $principalOwed = $loan->total_amount - $loan->paid_amount;
                    $interest = $principalOwed * $loan->interest_rate;

                    // Sumar el interés al contador de intereses pendientes
                    $loan->accrued_interest_balance += $interest;

                    // Actualizar la fecha del último cargo
                    $loan->last_interest_accrued_on = $today;

                    $loan->save();

                    $this->info("Interest accrued for loan '{$loan->name}'. Interest: {$interest}");
                });
            }
        }

        $this->info('Monthly loan interest process finished.');
        return 0;
    }
}
