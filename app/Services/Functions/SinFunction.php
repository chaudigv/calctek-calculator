<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;

class SinFunction implements FunctionInterface
{
    public function name(): string
    {
        return 'sin';
    }

    public function apply(float $arg): float
    {
        return sin($arg);
    }
}
