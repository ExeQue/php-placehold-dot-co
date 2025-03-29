<?php

declare(strict_types=1);

namespace Tests\Cache\Distinct\Concerns;

use DateInterval;
use ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns\Multiple;

it('reports false if action fails on multiple', function () {
    $cache = new class () {
        use Multiple;

        public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
        {
            return false;
        }

        public function get(string $key, mixed $default = null): mixed
        {
            // Not used
        }

        public function delete(string $key): bool
        {
            return false;
        }
    };

    expect($cache->setMultiple(['foo' => 'bar']))->toBeFalse()
        ->and($cache->deleteMultiple(['foo']))->toBeFalse();
});
