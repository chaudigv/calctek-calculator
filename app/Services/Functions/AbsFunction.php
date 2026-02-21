<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;

class AbsFunction implements FunctionInterface
{
    public function name(): string
    {
        return 'abs';
    }

    public function apply(float $arg): float
    {
        return abs($arg);
    }
}
