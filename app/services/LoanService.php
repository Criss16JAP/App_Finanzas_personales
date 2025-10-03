<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoanService
{
    // Inyectamos el MovementService para poder usarlo aquí
    public function __construct(private MovementService $movementService)
    {
    }

    public function getDataForLoanView(User $user)
    {
        return [
            // Pasamos las cuentas para el dropdown del formulario
            'accounts' => $user->accounts()->where('type', '!=', 'loan')->get(),
            // Pasamos los préstamos existentes para la lista
            'loans' => $user->loans()->where('status', 'pending')->orderBy('loan_date')->get(),
        ];
    }

    public function createLoan(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            // 1. Crear el registro del préstamo base
            $loan = $user->loans()->create([
                'name' => $data['name'],
                'borrower_name' => $data['borrower_name'],
                'total_amount' => $data['total_amount'], // Capital prestado
                'interest_rate' => $data['interest_rate'] / 100,
                'term_months' => $data['term_months'],
                'payment_day_of_month' => $data['payment_day_of_month'],
                'loan_date' => $data['loan_date'],
                'status' => 'pending',
            ]);

            // 2. Lógica del "interés por adelantado"
            $firstMonthInterest = $loan->total_amount * $loan->interest_rate;

            // 3. Actualizar el préstamo con el interés del primer mes
            $loan->accrued_interest_balance = $firstMonthInterest;
            $loan->last_interest_accrued_on = $loan->loan_date; // Marcamos que ya se cobró el interés de este ciclo
            $loan->save();

            // 4. Registrar el desembolso del dinero (movimiento de gasto)
            $movementData = [
                'type' => 'egress',
                'amount' => $data['total_amount'], // El gasto es solo por el capital
                'account_id' => $data['account_id'],
                'category_id' => $data['category_id'],
                'description' => "Desembolso de préstamo: {$loan->name}",
                'movement_date' => $data['loan_date'],
            ];
            $this->movementService->createMovement($movementData, $user);
        });
    }

    public function addRepayment(Loan $loan, array $data): void
    {
        DB::transaction(function () use ($loan, $data) {
            $paymentAmount = $data['amount'];
            $amountRemaining = $paymentAmount;

            // 1. Prioridad 1: Cubrir los intereses ganados que están pendientes
            $interestReceived = min($amountRemaining, $loan->accrued_interest_balance);
            $loan->accrued_interest_balance -= $interestReceived;
            $amountRemaining -= $interestReceived;

            // 2. Prioridad 2: El resto es abono a capital
            $principalReceived = $amountRemaining;

            // 3. Crear el movimiento de INGRESO
            $movementData = [
                'type' => 'income',
                'amount' => $paymentAmount,
                'account_id' => $data['account_id'], // La cuenta donde entra el dinero
                'category_id' => $data['category_id'],
                'description' => "Abono recibido de préstamo: {$loan->name}",
                'movement_date' => now(),
            ];
            $movement = $this->movementService->createMovement($movementData, $loan->user);

            // 4. Actualizar el monto de capital que te han pagado
            $loan->paid_amount += $principalReceived;
            if ($loan->paid_amount >= $loan->total_amount) {
                $loan->status = 'paid';
            }
            $loan->save();

            // 5. Crear el registro detallado del pago recibido en 'loan_payments'
            $loan->payments()->create([
                'movement_id' => $movement->id,
                'amount_received' => $paymentAmount,
                'interest_received' => $interestReceived,
                'principal_received' => $principalReceived,
                'payment_date' => now(),
            ]);
        });
    }

    public function getDataForLoanDetailView(Loan $loan)
    {
        $payments = $loan->payments;

        $totalPrincipalReceived = $payments->sum('principal_received');
        $totalInterestReceived = $payments->sum('interest_received');
        $totalReceived = $payments->sum('amount_received');

        $installmentsReceived = $payments->count();
        $remainingInstallments = $loan->term_months - $installmentsReceived;

        // Calcular la cuota fija mensual estimada (Fórmula de Amortización)
        $monthlyPayment = 0;
        $P = $loan->total_amount; // Principal
        $i = $loan->interest_rate;  // Tasa de interés mensual
        $n = $loan->term_months;      // Número de plazos

        if ($i > 0) {
            $monthlyPayment = $P * ($i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1);
        } else if ($n > 0) {
            $monthlyPayment = $P / $n;
        }

        $totalProjectedIncome = $monthlyPayment * $n;

        return [
            'loan' => $loan,
            'totalPrincipalReceived' => $totalPrincipalReceived,
            'totalInterestReceived' => $totalInterestReceived,
            'totalReceived' => $totalReceived,
            'installmentsReceived' => $installmentsReceived,
            'remainingInstallments' => $remainingInstallments,
            'monthlyPayment' => $monthlyPayment,
            'totalProjectedIncome' => $totalProjectedIncome,
        ];
    }
}
