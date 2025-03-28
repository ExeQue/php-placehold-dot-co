<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithSize;

class InteractsWithSizeImplementation
{
    use InteractsWithSize {
        renderSize as public;
    }
}
