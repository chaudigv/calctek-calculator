<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;
use App\Exceptions\ExpressionParseException;

class SqrtFunction implements FunctionInterface
{
    public function name(): string
    {
        return 'sqrt';
    }

    public function apply(float $arg): float
    {
        if ($arg < 0) {
            throw ExpressionParseException::negativeSquareRoot();
        }

        return sqrt($arg);
    }
}
