<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ExeQue\PlaceholdDotCo\Concerns\Builder\ActsAsImmutable;

class ActsAsImmutableImplementation
{
    use ActsAsImmutable {
        mutate as public;
    }
}
