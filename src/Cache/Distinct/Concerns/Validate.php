<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns;

use ExeQue\PlaceholdDotCo\Exceptions\CacheKeyException;

trait Validate
{
    protected function ensureKeyValidity(mixed $key): string
    {
        if (is_string($key) === false) {
            throw new CacheKeyException('The key must be a string.');
        }

        if (empty($key)) {
            throw new CacheKeyException('The key cannot be empty.');
        }

        return $key;
    }
}
