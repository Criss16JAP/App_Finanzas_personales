<?php

namespace App\Console\Commands;

use App\Models\CreditCard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateCardStatements extends Command
{
    protected $signature = 'cards:generate-statements';
    protected $description = 'Generate monthly billing cycle statements for credit cards.';

    public function handle()
    {
        $this->info('Starting to generate credit card statements...');
        $todayDay = now()->day;

        // Buscamos tarjetas cuya fecha de corte sea hoy
        $cards = CreditCard::where('cutoff_day', $todayDay)->get();

        foreach ($cards as $card) {
            // Verificamos que no se haya generado ya un extracto este mes
            if ($card->last_statement_date && $card->last_statement_date->isSameMonth(now())) {
                $this->warn("Statement for '{$card->name}' already generated this month. Skipping.");
                continue;
            }

            DB::transaction(function () use ($card) {
                // 1. Calcular intereses sobre la deuda actual
                $interestCharged = $card->current_debt * $card->interest_rate;

                // 2. Obtener la cuota de manejo
                $feesCharged = $card->monthly_fee;

                // 3. Calcular el total de las cuotas de las compras del mes
                $totalInstallmentsDue = 0;
                $purchasesToUpdate = [];
                // Buscamos todas las compras con cuotas pendientes
                $activePurchases = $card->purchases()->whereRaw('installments_paid < installments')->get();

                foreach ($activePurchases as $purchase) {
                    $totalInstallmentsDue += $purchase->purchase_amount / $purchase->installments;
                    $purchase->installments_paid++;
                    $purchasesToUpdate[] = $purchase;
                }

                // 4. Calcular el total a pagar en el extracto
                $closingBalance = $totalInstallmentsDue + $interestCharged + $feesCharged;

                // 5. Crear el registro del ciclo de facturación (el extracto)
                $card->billingCycles()->create([
                    'statement_date' => now(),
                    'due_date' => now()->setDay($card->payment_day),
                    'total_installments_due' => $totalInstallmentsDue,
                    'interest_charged' => $interestCharged,
                    'fees_charged' => $feesCharged,
                    'closing_balance' => $closingBalance,
                ]);

                // 6. Actualizar las cuotas pagadas de cada compra
                foreach ($purchasesToUpdate as $purchase) {
                    $purchase->save();
                }

                // 7. Actualizar la fecha del último extracto en la tarjeta
                $card->last_statement_date = now();
                $card->save();

                $this->info("Statement generated for card '{$card->name}'.");
            });
        }

        $this->info('Statement generation process finished.');
        return 0;
    }
}
