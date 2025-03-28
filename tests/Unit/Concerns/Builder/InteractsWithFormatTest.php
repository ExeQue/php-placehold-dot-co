<?php

declare(strict_types=1);

namespace Tests\Concerns\Builder;

use ExeQue\PlaceholdDotCo\Data\Format;
use Tests\Fixtures\InteractsWithFormatImplementation;

it('is immutable', function () {
    $original = new InteractsWithFormatImplementation();

    foreach (Format::cases() as $format) {
        $mutated = $original->format($format);
        expect($mutated)->not->toBe($original);
    }

    $mutated = $original->png();
    expect($mutated)->not->toBe($original);

    $mutated = $original->jpeg();
    expect($mutated)->not->toBe($original);

    $mutated = $original->webp();
    expect($mutated)->not->toBe($original);

    $mutated = $original->svg();
    expect($mutated)->not->toBe($original);

    $mutated = $original->avif();
    expect($mutated)->not->toBe($original);

    $mutated = $original->gif();
    expect($mutated)->not->toBe($original);
});

it('can set format', function () {
    $builder = new InteractsWithFormatImplementation();

    foreach (Format::cases() as $format) {
        $mutated = $builder->format($format);
        expect($mutated->renderFormat())->toBe($format->value);
    }
});

it('sets the format correctly using specific format methods', function () {
    $builder = new InteractsWithFormatImplementation();

    $builder = $builder->png();
    expect($builder->getFormat())->toBe(Format::PNG)
        ->and($builder->renderFormat())->toBe(Format::PNG->value);

    $builder = $builder->jpeg();
    expect($builder->getFormat())->toBe(Format::JPEG)
        ->and($builder->renderFormat())->toBe(Format::JPEG->value);

    $builder = $builder->webp();
    expect($builder->getFormat())->toBe(Format::WEBP)
        ->and($builder->renderFormat())->toBe(Format::WEBP->value);

    $builder = $builder->svg();
    expect($builder->getFormat())->toBe(Format::SVG)
        ->and($builder->renderFormat())->toBe(Format::SVG->value);

    $builder = $builder->avif();
    expect($builder->getFormat())->toBe(Format::AVIF)
        ->and($builder->renderFormat())->toBe(Format::AVIF->value);

    $builder = $builder->gif();
    expect($builder->getFormat())->toBe(Format::GIF)
        ->and($builder->renderFormat())->toBe(Format::GIF->value);
});
