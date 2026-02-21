<?php

namespace App\Services\Operators;

use App\Contracts\OperatorInterface;

class AdditionOperator implements OperatorInterface
{
    public function symbol(): string
    {
        return '+';
    }

    public function precedence(): int
    {
        return 1;
    }

    public function associativity(): string
    {
        return 'left';
    }

    public function apply(float $left, float $right): float
    {
        return $left + $right;
    }
}
