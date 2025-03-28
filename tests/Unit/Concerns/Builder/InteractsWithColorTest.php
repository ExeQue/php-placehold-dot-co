<?php

declare(strict_types=1);

namespace Tests\Concerns\Builder;

use Tests\Fixtures\InteractsWithColorImplementation;

it('is immutable', function () {
    $original = new InteractsWithColorImplementation();

    $mutated = $original->color('white', 'black');
    expect($mutated)->not->toBe($original);

    $mutated = $original->background('black');
    expect($mutated)->not->toBe($original);

    $mutated = $original->foreground('white');
    expect($mutated)->not->toBe($original);
});

it('can set the color', function () {
    $builder = new InteractsWithColorImplementation();

    $builder = $builder->color('white', 'black');

    expect($builder->renderColor())->toBe([
        'background' => 'white',
        'foreground' => 'black',
    ]);
});

it('can set the background', function () {
    $builder = new InteractsWithColorImplementation();

    $builder = $builder->color('white', 'black');
    $builder = $builder->background('black');

    expect($builder->renderColor())->toBe([
        'background' => 'black',
        'foreground' => 'black',
    ]);
});

it('can set the foreground', function () {
    $builder = new InteractsWithColorImplementation();

    $builder = $builder->color('white', 'black');
    $builder = $builder->foreground('white');

    expect($builder->renderColor())->toBe([
        'background' => 'white',
        'foreground' => 'white',
    ]);
});

it('sets the background to white if not set when setting foreground', function () {
    $builder = new InteractsWithColorImplementation();

    $builder = $builder->foreground('white');

    expect($builder->renderColor())->toBe([
        'background' => 'white',
        'foreground' => 'white',
    ]);
});
