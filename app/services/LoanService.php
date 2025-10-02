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
        // Usamos una transacción para asegurar la integridad de los datos
        DB::transaction(function () use ($data, $user) {
            // 1. Crear el registro del préstamo
            $loan = $user->loans()->create([
                'name' => $data['name'],
                'borrower_name' => $data['borrower_name'],
                'total_amount' => $data['amount'],
                'loan_date' => $data['loan_date'],
            ]);

            // 2. Preparar los datos para registrar la salida de dinero
            $movementData = [
                'type' => 'egress',
                'amount' => $data['amount'],
                'account_id' => $data['account_id'], // La cuenta de donde sale el dinero
                'category_id' => $data['category_id'], // La categoría "Préstamos"
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
        // 1. Actualizar el préstamo
        $loan->paid_amount += $data['amount'];

        // 2. Si el monto pagado es igual o mayor al total, marcar como pagado
        if ($loan->paid_amount >= $loan->total_amount) {
            $loan->status = 'paid';
        }
        $loan->save();

        // 3. Preparar los datos para el movimiento de ingreso
        $movementData = [
            'type' => 'income',
            'amount' => $data['amount'],
            'account_id' => $data['account_id'], // La cuenta donde entra el dinero
            'category_id' => $data['category_id'],
            'description' => "Abono de préstamo: {$loan->name}",
            'movement_date' => now(), // Usamos la fecha actual para el pago
        ];

        // 4. Crear el movimiento de ingreso usando el MovementService
        $this->movementService->createMovement($movementData, $loan->user);
    });
}
}
