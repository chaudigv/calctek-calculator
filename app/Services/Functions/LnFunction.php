<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;

class LnFunction implements FunctionInterface
{
    public function name(): string
    {
        return 'ln';
    }

    public function apply(float $arg): float
    {
        return log($arg);
    }
}
