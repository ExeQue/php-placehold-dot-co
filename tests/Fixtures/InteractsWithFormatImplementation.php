<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithFormat;

class InteractsWithFormatImplementation
{
    use InteractsWithFormat {
        renderFormat as public;
    }
}
