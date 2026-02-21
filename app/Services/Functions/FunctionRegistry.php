<?php

namespace App\Services\Functions;

use App\Contracts\FunctionInterface;
use App\Services\Registry;

/**
 * @extends Registry<FunctionInterface>
 */
class FunctionRegistry extends Registry
{
    protected function getKey(mixed $item): string
    {
        return $item->name();
    }

    public function get(string $key): FunctionInterface
    {
        return parent::get($key);
    }

    /** @return array<string> */
    public function names(): array
    {
        return $this->keys();
    }
}
