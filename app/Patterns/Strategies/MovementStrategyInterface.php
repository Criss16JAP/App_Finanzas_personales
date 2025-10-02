<?php

namespace App\Patterns\Strategies;

use App\Models\Movement;
use App\Models\User;

interface MovementStrategyInterface
{
    public function execute(array $data, User $user): void;

    // MÉTODO AÑADIDO
    public function revert(Movement $movement): void;
}
