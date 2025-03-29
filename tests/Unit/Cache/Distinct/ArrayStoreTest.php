<?php

declare(strict_types=1);

use ExeQue\PlaceholdDotCo\Cache\Distinct\ArrayStore;
use Psr\SimpleCache\CacheInterface;

covers(ArrayStore::class);

arch()->expect(ArrayStore::class)->toImplement(CacheInterface::class);

storeTests(fn () => new ArrayStore());
