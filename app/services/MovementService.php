<?php

namespace App\Services;

use App\Models\User;
use App\Patterns\Strategies\EgressStrategy;
use App\Patterns\Strategies\IncomeStrategy;
use App\Patterns\Strategies\TransferStrategy;

class MovementService
{
    protected array $strategies;

    public function __construct()
    {
        $this->strategies = [
            'income' => new IncomeStrategy(),
            'egress' => new EgressStrategy(),
            'transfer' => new TransferStrategy(),
        ];
    }

    public function createMovement(array $data, User $user): void
    {
        $type = $data['type'];

        if (!isset($this->strategies[$type])) {
            throw new \InvalidArgumentException("Tipo de movimiento no válido.");
        }

        $this->strategies[$type]->execute($data, $user);
    }

    public function getDataForMovementView(User $user)
{
    return [
        'accounts' => $user->accounts()->get(),
        'incomeCategories' => $user->categories()->where('type', 'income')->get(),
        'egressCategories' => $user->categories()->where('type', 'egress')->get(),
        'movements' => $user->movements()->with(['account', 'category', 'relatedAccount'])->latest()->take(15)->get(),
    ];
}
}
