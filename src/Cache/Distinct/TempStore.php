<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct;

use DateInterval;
use DateTimeInterface;

class TempStore extends Store
{
    /** @var array<array{resource: resource, expires: DateTimeInterface}>  */
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

            $resource = $stored['resource'];

            rewind($resource);

            return unserialize(stream_get_contents($resource), ['allowed_classes' => true]);
        }

        return $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $key = $this->ensureKeyValidity($key);

        // @codeCoverageIgnoreStart
        if ($this->has($key)) {
            $this->delete($key);
        }
        // @codeCoverageIgnoreEnd

        $expires = $this->resolveTtl($ttl);

        $resource = fopen('php://temp', 'wrb');

        fwrite($resource, serialize($value));

        rewind($resource);

        $this->cache[$key] = [
            'resource' => $resource,
            'expires' => $expires,
        ];

        return true;
    }

    public function delete(string $key): bool
    {
        $key = $this->ensureKeyValidity($key);

        if (array_key_exists($key, $this->cache)) {
            $stored = $this->cache[$key];

            fclose($stored['resource']);

            unset($this->cache[$key]);
            return true;
        }

        return false;
    }

    public function clear(): bool
    {
        foreach ($this->cache as $item) {
            fclose($item['resource']);
        }

        $this->cache = [];

        return true;
    }

    public function has(string $key): bool
    {
        $key = $this->ensureKeyValidity($key);

        return array_key_exists($key, $this->cache) && $this->isExpired($this->cache[$key]['expires']) === false;
    }

    public function __destruct()
    {
        $this->clear();
    }
}
