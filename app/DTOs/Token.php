<?php

namespace App\DTOs;

use App\Enums\TokenType;

readonly class Token
{
    public function __construct(
        public TokenType $type,
        public string|float $value,
    ) {}

    public function is(TokenType $type): bool
    {
        return $this->type === $type;
    }

    public function isNot(TokenType $type): bool
    {
        return $this->type !== $type;
    }
}
