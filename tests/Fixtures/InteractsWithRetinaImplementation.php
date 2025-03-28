<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithRetina;
use ExeQue\PlaceholdDotCo\Data\Format;

class InteractsWithRetinaImplementation
{
    use InteractsWithRetina {
        renderRetina as public;
    }

    public function __construct(
        private Format $format = Format::PNG
    ) {
    }

    public function getFormat(): Format
    {
        return $this->format;
    }
}
