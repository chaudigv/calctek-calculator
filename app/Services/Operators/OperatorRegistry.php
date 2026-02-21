<?php

namespace App\Services\Operators;

use App\Contracts\OperatorInterface;
use App\Services\Registry;

/**
 * @extends Registry<OperatorInterface>
 */
class OperatorRegistry extends Registry
{
    protected function getKey(mixed $item): string
    {
        return $item->symbol();
    }

    public function get(string $key): OperatorInterface
    {
        return parent::get($key);
    }

    /** @return array<string> */
    public function symbols(): array
    {
        return $this->keys();
    }

    /** @return array<string> */
    public function getByPrecedence(int $precedence): array
    {
        return array_keys(
            array_filter(
                $this->items,
                fn (OperatorInterface $op) => $op->precedence() === $precedence
            )
        );
    }
}
