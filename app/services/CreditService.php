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
    }
}
