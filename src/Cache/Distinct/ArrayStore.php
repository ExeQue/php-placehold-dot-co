<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct;

use DateInterval;
use DateTimeInterface;

class ArrayStore extends Store
{
    /** @var array<array{value: mixed, expires: DateTimeInterface}>  */
    private array $cache = [];

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->ensureKeyValidity($key);

        if (array_key_exists($key, $this->cache)) {
            $stored = $this->cache[$key];

            /** @var DateTimeInterface $expires */
            $expires = $stored['expires'];

            if ($this->isExpired($expires)) {
                unset($this->cache[$key]);

                return $default;
            }

            return $stored['value'];
        }

        return $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $key = $this->ensureKeyValidity($key);

        $expires = $this->resolveTtl($ttl);

        $this->cache[$key] = [
            'value' => $value,
            'expires' => $expires,
        ];

        return true;
    }

    public function delete(string $key): bool
    {
        $key = $this->ensureKeyValidity($key);

        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);
            return true;
        }

        return false;
    }

    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    public function has(string $key): bool
    {
        $key = $this->ensureKeyValidity($key);

        return array_key_exists($key, $this->cache) && $this->isExpired($this->cache[$key]['expires']) === false;
    }
}
