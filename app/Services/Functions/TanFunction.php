<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;

class TanFunction implements FunctionInterface
{
    public function name(): string
    {
        return 'tan';
    }

    public function apply(float $arg): float
    {
        return tan($arg);
    }
}
