<?php

declare(strict_types=1);

namespace Tests\Concerns\Builder;

use Tests\Fixtures\InteractsWithSizeImplementation;

it('is immutable', function () {
    $original = new InteractsWithSizeImplementation();

    $mutated = $original->width(400);
    expect($mutated)->not->toBe($original);

    $mutated = $original->height(400);
    expect($mutated)->not->toBe($original);

    $mutated = $original->size(400, 400);
    expect($mutated)->not->toBe($original);

    $mutated = $original->square(400);
    expect($mutated)->not->toBe($original);

    $mutated = $original->landscape();
    expect($mutated)->not->toBe($original);

    $mutated = $original->portrait();
    expect($mutated)->not->toBe($original);
});

it('has default size', function () {
    $builder = new InteractsWithSizeImplementation();

    expect($builder->renderSize())->toBe('300x300');
});

it('can set the size', function () {
    $builder = (new InteractsWithSizeImplementation())->size(0, 0);

    $mutated = $builder->size(400, 300);
    expect($mutated->renderSize())->toBe('400x300');

    $mutated = $builder->width(400);
    expect($mutated->renderSize())->toBe('400x0');

    $mutated = $builder->height(400);
    expect($mutated->renderSize())->toBe('0x400');

    $mutated = $builder->square(400);
    expect($mutated->renderSize())->toBe('400x400');
});

it('can set the size to landscape', function () {
    $builder = new InteractsWithSizeImplementation();

    $mutated = $builder->size(1080, 1920);
    expect($mutated->renderSize())->toBe('1080x1920');

    $mutated = $mutated->landscape();
    expect($mutated->renderSize())->toBe('1920x1080');
});

it('can set the size to portrait', function () {
    $builder = new InteractsWithSizeImplementation();

    $mutated = $builder->size(1920, 1080);
    expect($mutated->renderSize())->toBe('1920x1080');

    $mutated = $mutated->portrait();
    expect($mutated->renderSize())->toBe('1080x1920');
});
