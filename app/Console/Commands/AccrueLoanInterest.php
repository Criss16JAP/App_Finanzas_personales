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
            // La fecha de cargo es 5 días antes del día de cobro establecido
            $chargeDate = now()->setDay($loan->payment_day_of_month)->subDays(5)->startOfDay();

            // Verificamos si hoy es el día de cargo y si no se ha ejecutado ya en este ciclo de pago.
            // Gracias a que 'last_interest_accrued_on' se establece al crear el préstamo,
            // esta condición saltará el primer mes automáticamente.
            if ($today->isSameDay($chargeDate) && (is_null($loan->last_interest_accrued_on) || $loan->last_interest_accrued_on < $chargeDate)) {

                DB::transaction(function () use ($loan, $today) {
                    // Calcular interés sobre el capital pendiente de cobro
                    $principalOwed = $loan->total_amount - $loan->paid_amount;

                    // Solo calculamos interés si aún se debe capital
                    if ($principalOwed > 0) {
                        $interest = $principalOwed * $loan->interest_rate;

                        // Sumar el interés al contador de intereses pendientes por cobrar
                        $loan->accrued_interest_balance += $interest;

                        // Actualizar la fecha del último cargo
                        $loan->last_interest_accrued_on = $today;

                        $loan->save();

                        $this->info("Interest accrued for loan '{$loan->name}'. Interest: {$interest}");
                    }
                });
            }
        }

        $this->info('Monthly loan interest process finished.');
        return 0;
    }
}
