<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct;

use ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns\Multiple;
use ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns\ResolvesTtl;
use ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns\Validate;
use Psr\SimpleCache\CacheInterface;

abstract class Store implements CacheInterface
{
    use ResolvesTtl;
    use Validate;
    use Multiple;
}
