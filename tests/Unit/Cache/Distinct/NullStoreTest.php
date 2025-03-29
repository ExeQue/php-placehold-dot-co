<?php

declare(strict_types=1);

use ExeQue\PlaceholdDotCo\Cache\Distinct\NullStore;
use Psr\SimpleCache\CacheInterface;

arch()->expect(NullStore::class)->toImplement(CacheInterface::class);
