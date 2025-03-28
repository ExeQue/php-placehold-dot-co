<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithText;

class InteractsWithTextImplementation
{
    use InteractsWithText {
        renderText as public;
        hasText as public;
    }
}
