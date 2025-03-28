<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithColor;

/**
 * @internal
 */
class InteractsWithColorImplementation
{
    use InteractsWithColor {
        renderColor as public;
    }
}
