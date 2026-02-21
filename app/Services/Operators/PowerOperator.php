<?php

namespace App\Services\Operators;

use App\Contracts\OperatorInterface;

class PowerOperator implements OperatorInterface
{
    public function symbol(): string
    {
        return '^';
    }

    public function precedence(): int
    {
        return 3;
    }

    public function associativity(): string
    {
        return 'right';
    }

    public function apply(float $left, float $right): float
    {
        return pow($left, $right);
    }
}
