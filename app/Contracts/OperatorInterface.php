<?php

namespace App\Contracts;

interface OperatorInterface
{
    public function symbol(): string;

    public function precedence(): int;

    public function associativity(): string;

    public function apply(float $left, float $right): float;
}
