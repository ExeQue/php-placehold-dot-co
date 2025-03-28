<?php

declare(strict_types=1);

namespace Tests\Concerns\Builder;

use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Exceptions\PlaceholdException;
use Tests\Fixtures\InteractsWithRetinaImplementation;

it('is immutable', function () {
    $original = new InteractsWithRetinaImplementation();

    $mutated = $original->x1();
    expect($mutated)->not->toBe($original);

    $mutated = $original->x2();
    expect($mutated)->not->toBe($original);

    $mutated = $original->x3();
    expect($mutated)->not->toBe($original);
});

it('sets the retina correctly', function () {
    $builder = new InteractsWithRetinaImplementation();

    $builder = $builder->x1();
    expect($builder->renderRetina())->toBe('');

    $builder = $builder->x2();
    expect($builder->renderRetina())->toBe('@2x');

    $builder = $builder->x3();
    expect($builder->renderRetina())->toBe('@3x');
});

it('throws exception when retina is not supported by the format', function (Format $format, bool $supported) {
    $builder = new InteractsWithRetinaImplementation($format);

    if ($supported === true) {
        $this->expectNotToPerformAssertions();
        $builder->x2()->renderRetina();
    } else {
        expect(fn () => $builder->x2()->renderRetina())
            ->toThrow(PlaceholdException::class, 'Retina is not supported for this format');
    }
})->with([
    'png' => [
        'format' => Format::PNG,
        'supported' => true,
    ],
    'jpeg' => [
        'format' => Format::JPEG,
        'supported' => true,
    ],
    'gif' => [
        'format' => Format::GIF,
        'supported' => true,
    ],
    'webp' => [
        'format' => Format::WEBP,
        'supported' => true,
    ],
    'svg' => [
        'format' => Format::SVG,
        'supported' => false,
    ],
    'avif' => [
        'format' => Format::AVIF,
        'supported' => true,
    ],
]);
