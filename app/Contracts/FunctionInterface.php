<?php

namespace App\Contracts;

interface FunctionInterface
{
    public function name(): string;

    public function apply(float $arg): float;
}
