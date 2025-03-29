<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Contracts;

use DateInterval;
use DateTimeInterface;

interface ImageStore
{
    public function get(string $uri): mixed;

    public function set(string $uri, mixed $raw, DateInterval|DateTimeInterface|null $ttl = null): bool;

    public function delete(string $uri): bool;

    public function has(string $uri): bool;

    public function remember(string $uri, callable $callback, DateInterval|DateTimeInterface|null $ttl = null): mixed;
}
