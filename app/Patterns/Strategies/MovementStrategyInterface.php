<?php

namespace App\Patterns\Strategies;

use App\Models\User;

interface MovementStrategyInterface
{
    public function execute(array $data, User $user): void;
}
