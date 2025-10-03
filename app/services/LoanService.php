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
            // 1. Crear el registro del préstamo con los nuevos campos
            $loan = $user->loans()->create([
                'name' => $data['name'],
                'borrower_name' => $data['borrower_name'],
                'total_amount' => $data['total_amount'],
                'interest_rate' => $data['interest_rate'] / 100, // Convertir % a decimal
                'term_months' => $data['term_months'],
                'payment_day_of_month' => $data['payment_day_of_month'],
                'loan_date' => $data['loan_date'],
                'paid_amount' => 0, // Asegurarnos que el monto pagado inicie en 0
                'status' => 'pending',
            ]);

            // 2. Preparar los datos para registrar la salida de dinero
            $movementData = [
                'type' => 'egress',
                'amount' => $data['total_amount'],
                'account_id' => $data['account_id'],
                'category_id' => $data['category_id'],
                'description' => "Desembolso de préstamo: {$loan->name}",
                'movement_date' => $data['loan_date'],
            ];

            // 3. Usar el MovementService para crear el movimiento de gasto
            $this->movementService->createMovement($movementData, $user);
        });
    }

    public function addRepayment(Loan $loan, array $data): void
    {
        DB::transaction(function () use ($loan, $data) {
            $paymentAmount = $data['amount'];
            $amountRemaining = $paymentAmount;

            // 1. Prioridad 1: Cubrir los intereses ganados pendientes
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

            // 4. Actualizar el monto de capital pagado en el préstamo
            $loan->paid_amount += $principalReceived;
            if ($loan->paid_amount >= $loan->total_amount) {
                $loan->status = 'paid';
            }
            $loan->save();

            // 5. Crear el registro detallado del pago recibido
            $loan->payments()->create([
                'movement_id' => $movement->id,
                'amount_received' => $paymentAmount,
                'interest_received' => $interestReceived,
                'principal_received' => $principalReceived,
                'payment_date' => now(),
            ]);
        });
    }
}
