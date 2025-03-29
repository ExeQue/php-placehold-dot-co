<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct;

use DateInterval;

/**
 * @codeCoverageIgnore
 */
class NullStore extends Store
{
    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureKeyValidity($key);

        return $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->ensureKeyValidity($key);

        return true;
    }

    public function delete(string $key): bool
    {
        $this->ensureKeyValidity($key);

        return true;
    }

    public function clear(): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        $this->ensureKeyValidity($key);

        return false;
    }
}
