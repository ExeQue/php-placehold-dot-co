<?php

declare(strict_types=1);

namespace Tests\Data;

use ExeQue\PlaceholdDotCo\Data\Format;
use InvalidArgumentException;

it('has correct mime types', function () {
    expect(Format::PNG->mime())->toBe('image/png')
        ->and(Format::JPEG->mime())->toBe('image/jpeg')
        ->and(Format::WEBP->mime())->toBe('image/webp')
        ->and(Format::SVG->mime())->toBe('image/svg+xml')
        ->and(Format::GIF->mime())->toBe('image/gif')
        ->and(Format::AVIF->mime())->toBe('image/avif');
});
