<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;

class CosFunction implements FunctionInterface
{
    public function name(): string
    {
        return 'cos';
    }

    public function apply(float $arg): float
    {
        return cos($arg);
    }
}
