<?php

namespace App\Console\Commands;

use App\Models\Credit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccrueCreditCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:accrue-charges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates and adds monthly interest and fixed fees to active credits.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to accrue monthly credit charges...');

        $today = now()->startOfDay();

        $activeCredits = Credit::where('status', 'active')->get();

        foreach ($activeCredits as $credit) {
            // La fecha de cargo es 5 días antes del día de pago
            $chargeDate = now()
                ->setDay($credit->payment_day_of_month)
                ->subDays(5)
                ->startOfDay();

            // Verificamos si hoy es el día de cargo y si no se ha ejecutado ya este mes
            if ($today->isSameDay($chargeDate) && (is_null($credit->last_interest_accrued_on) || $credit->last_interest_accrued_on < $chargeDate)) {

                DB::transaction(function () use ($credit, $today) {
                    // 1. Calcular interés sobre el saldo actual
                    $interest = $credit->current_balance * $credit->interest_rate;

                    // 2. Obtener la tarifa fija
                    $fee = $credit->fixed_monthly_fee;

                    // 3. Actualizar los saldos
                    $credit->current_balance += $interest + $fee; // Aumentar la deuda total
                    $credit->accrued_interest_balance += $interest; // Aumentar el contador de intereses pendientes
                    $credit->accrued_fee_balance += $fee; // Aumentar el contador de tarifas pendientes

                    // 4. Actualizar la fecha del último cargo
                    $credit->last_interest_accrued_on = $today;

                    $credit->save();

                    $this->info("Charges accrued for credit '{$credit->name}'. Interest: {$interest}, Fee: {$fee}");
                });
            }
        }

        $this->info('Monthly credit charges process finished.');
        return 0;
    }
}
