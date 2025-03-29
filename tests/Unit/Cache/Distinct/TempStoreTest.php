<?php

declare(strict_types=1);

use ExeQue\PlaceholdDotCo\Cache\Distinct\TempStore;
use Psr\SimpleCache\CacheInterface;

covers(TempStore::class);

arch()->expect(TempStore::class)->toImplement(CacheInterface::class);

storeTests(fn () => new TempStore());
