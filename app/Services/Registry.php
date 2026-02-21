<?php

namespace App\Services;

/**
 * @template T
 */
abstract class Registry
{
    /** @var array<string, T> */
    protected array $items = [];

    /**
     * @param T $item
     */
    abstract protected function getKey(mixed $item): string;

    /**
     * @param T $item
     * @return $this
     */
    public function register(mixed $item): static
    {
        $this->items[$this->getKey($item)] = $item;

        return $this;
    }

    /**
     * @return T|null
     */
    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->items[$key]);
    }

    /** @return array<string> */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /** @return array<string, T> */
    public function all(): array
    {
        return $this->items;
    }
}
