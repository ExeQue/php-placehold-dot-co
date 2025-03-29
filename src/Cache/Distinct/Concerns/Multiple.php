<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns;

use DateInterval;

trait Multiple
{
    use Validate;

    abstract public function get(string $key, mixed $default = null): mixed;

    abstract public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool;

    abstract public function delete(string $key): bool;

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $values = [];

        foreach ($keys as $key) {
            $key = $this->ensureKeyValidity($key);

            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $key = $this->ensureKeyValidity($key);

            if ($this->set($key, $value, $ttl) === false) {
                return false;
            }
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $key = $this->ensureKeyValidity($key);

            if ($this->delete($key) === false) {
                return false;
            }
        }

        return true;
    }
}
