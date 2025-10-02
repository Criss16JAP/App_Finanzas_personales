<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function __construct(private MovementService $movementService)
    {
    }

    public function getDataForCreditView(User $user)
    {
        return [
            'accounts' => $user->accounts()->whereIn('type', ['bank', 'cash'])->get(),
            'credits' => $user->credits()->where('status', 'active')->orderBy('issued_date')->get(),
        ];
    }

    public function createCredit(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            $credit = $user->credits()->create([
                'name' => $data['name'],
                'description' => $data['description'],
                'principal_amount' => $data['principal_amount'],
                'current_balance' => $data['principal_amount'],
                'interest_rate' => $data['interest_rate'] / 100,
                'term_months' => $data['term_months'],
                'fixed_monthly_fee' => $data['fixed_monthly_fee'] ?? 0, // <-- AÑADIR
                'issued_date' => $data['issued_date'],
                'payment_day_of_month' => $data['payment_day_of_month'],
            ]);

            $movementData = [
                'type' => 'income',
                'amount' => $data['principal_amount'],
                'account_id' => $data['account_id_deposit'],
                'description' => "Recepción de crédito: {$credit->name}",
                'movement_date' => $data['issued_date'],
            ];

            $this->movementService->createMovement($movementData, $user);
        });
    }

    /* Fue refactorizado
     public function addPayment(Credit $credit, array $data): void
     {
         DB::transaction(function () use ($credit, $data) {
             $movementData = [
                 'type' => 'egress',
                 'amount' => $data['amount'],
                 'account_id' => $data['account_id'],
                 'category_id' => $data['category_id'],
                 'description' => "Pago de crédito: {$credit->name}",
                 'movement_date' => now(),
             ];

             $movement = $this->movementService->createMovement($movementData, $credit->user);

             $credit->current_balance -= $data['amount'];
             if ($credit->current_balance <= 0) {
                 $credit->current_balance = 0;
                 $credit->status = 'paid';
             }
             $credit->save();

             $credit->payments()->create([
                 'movement_id' => $movement->id,
                 'amount_paid' => $data['amount'],
                 'principal_paid' => $data['amount'],
                 'interest_paid' => 0,
                 'payment_date' => now(),
             ]);
         });
     } */

    public function addPayment(Credit $credit, array $data): void
    {
        DB::transaction(function () use ($credit, $data) {
            $paymentAmount = $data['amount'];
            $amountRemaining = $paymentAmount;

            // --- Lógica de Distribución Inteligente ---

            // 1. Prioridad 1: Pagar las tarifas fijas pendientes
            $feePaid = min($amountRemaining, $credit->accrued_fee_balance);
            $credit->accrued_fee_balance -= $feePaid;
            $amountRemaining -= $feePaid;

            // 2. Prioridad 2: Pagar los intereses pendientes
            $interestPaid = min($amountRemaining, $credit->accrued_interest_balance);
            $credit->accrued_interest_balance -= $interestPaid;
            $amountRemaining -= $interestPaid;

            // 3. Prioridad 3: El resto es abono a capital
            $principalPaid = $amountRemaining;

            // --- Actualización y Registro ---

            // 4. Crear el movimiento de GASTO
            $movementData = [
                'type' => 'egress',
                'amount' => $paymentAmount,
                'account_id' => $data['account_id'],
                'category_id' => $data['category_id'],
                'description' => "Pago de crédito: {$credit->name}",
                'movement_date' => now(),
            ];
            $movement = $this->movementService->createMovement($movementData, $credit->user);

            // 5. Actualizar el saldo total del crédito
            $credit->current_balance -= $paymentAmount; // Se reduce el monto total pagado
            if ($credit->current_balance < 0.01) {
                $credit->current_balance = 0;
                $credit->status = 'paid';
            }
            $credit->save();

            // 6. Crear el registro detallado del pago
            $credit->payments()->create([
                'movement_id' => $movement->id,
                'amount_paid' => $paymentAmount,
                'fee_paid' => $feePaid,
                'interest_paid' => $interestPaid,
                'principal_paid' => $principalPaid,
                'payment_date' => now(),
            ]);
        });
    }

    public function getDataForCreditDetailView(Credit $credit)
    {
        // Cargar los pagos para evitar consultas adicionales
        $payments = $credit->payments;

        // 1. Calcular los totales pagados hasta la fecha
        $totalPrincipalPaid = $payments->sum('principal_paid');
        $totalInterestPaid = $payments->sum('interest_paid');
        $totalFeesPaid = $payments->sum('fee_paid');
        $totalPaid = $payments->sum('amount_paid');

        // 2. Calcular información de las cuotas
        $installmentsPaid = $payments->count();
        $remainingInstallments = $credit->term_months - $installmentsPaid;

        // 3. Calcular la cuota fija mensual estimada (Fórmula de Amortización)
        $monthlyPayment = 0;
        $P = $credit->principal_amount; // Principal
        $i = $credit->interest_rate;  // Tasa de interés mensual
        $n = $credit->term_months;      // Número de plazos

        if ($i > 0) {
            $monthlyInterestComponent = $P * ($i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1);
            $monthlyPayment = $monthlyInterestComponent + $credit->fixed_monthly_fee;
        } else {
            // Si no hay interés, la cuota es simplemente el capital más la tarifa
            $monthlyPayment = ($P / $n) + $credit->fixed_monthly_fee;
        }

        // 4. Calcular el costo total proyectado del crédito
        $totalProjectedPayment = $monthlyPayment * $n;

        return [
            'credit' => $credit,
            'totalPrincipalPaid' => $totalPrincipalPaid,
            'totalInterestPaid' => $totalInterestPaid,
            'totalFeesPaid' => $totalFeesPaid,
            'totalPaid' => $totalPaid,
            'installmentsPaid' => $installmentsPaid,
            'remainingInstallments' => $remainingInstallments,
            'monthlyPayment' => $monthlyPayment,
            'totalProjectedPayment' => $totalProjectedPayment,
        ];
    }
}
