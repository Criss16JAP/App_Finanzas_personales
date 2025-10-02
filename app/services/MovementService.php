<?php

namespace App\Services;

use App\Models\Movement;
use App\Models\User;
use App\Patterns\Strategies\EgressStrategy;
use App\Patterns\Strategies\IncomeStrategy;
use App\Patterns\Strategies\TransferStrategy;
use Illuminate\Support\Facades\DB;

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

    /* public function createMovement(array $data, User $user): void
    {
        $type = $data['type'];

        if (!isset($this->strategies[$type])) {
            throw new \InvalidArgumentException("Tipo de movimiento no válido.");
        }

        $this->strategies[$type]->execute($data, $user);
    } Metodo antiguo para crear un movimiento, se refactorizo */

    public function getDataForMovementView(User $user)
    {
        return [
            'accounts' => $user->accounts()->get(),
            'incomeCategories' => $user->categories()->where('type', 'income')->get(),
            'egressCategories' => $user->categories()->where('type', 'egress')->get(),
            'movements' => $user->movements()->with(['account', 'category', 'relatedAccount'])->latest()->take(15)->get(),
        ];
    }

    public function deleteMovement(Movement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $strategy = $this->strategies[$movement->type];
            $strategy->revert($movement);
            $movement->delete();
        });
    }

    public function updateMovement(Movement $movement, array $newData): void
    {
        DB::transaction(function () use ($movement, $newData) {
            $oldStrategy = $this->strategies[$movement->type];
            $oldStrategy->revert($movement);

            $movement->update($newData);

            $this->executeMovement($movement->fresh());
        });
    }

    // El nuevo método createMovement que usa el refactor
    public function createMovement(array $data, User $user): Movement // <-- Cambiado de void a Movement
    {
        // Usamos la función de transacción para que pueda devolver un valor
        return DB::transaction(function () use ($data, $user) {
            $movement = $user->movements()->create($data);
            $this->executeMovement($movement);

            return $movement; // <-- Devolvemos el movimiento creado
        });
    }

    // Método privado refactorizado
    private function executeMovement(Movement $movement): void
    {
        $strategy = $this->strategies[$movement->type];
        $strategy->execute($movement->toArray(), $movement->user);
    }
}
