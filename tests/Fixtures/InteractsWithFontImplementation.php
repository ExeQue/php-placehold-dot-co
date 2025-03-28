<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithFont;

class InteractsWithFontImplementation
{
    use InteractsWithFont {
        renderFont as public;
    }

    public function __construct(
        private readonly bool $hasText = true,
    ) {
    }

    private function hasText(): bool
    {
        return $this->hasText;
    }
}
