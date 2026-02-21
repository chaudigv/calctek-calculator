<?php

namespace App\Services\Operators;

use App\Contracts\OperatorInterface;
use App\Exceptions\ExpressionParseException;

class ModuloOperator implements OperatorInterface
{
    public function symbol(): string
    {
        return '%';
    }

    public function precedence(): int
    {
        return 2;
    }

    public function associativity(): string
    {
        return 'left';
    }

    public function apply(float $left, float $right): float
    {
        if ($right == 0) {
            throw ExpressionParseException::divisionByZero();
        }

        return fmod($left, $right);
    }
}
