<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;

class LogFunction implements FunctionInterface
{
    public function name(): string
    {
        return 'log';
    }

    public function apply(float $arg): float
    {
        return log10($arg);
    }
}
