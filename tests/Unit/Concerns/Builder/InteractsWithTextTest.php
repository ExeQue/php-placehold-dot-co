<?php

declare(strict_types=1);

namespace Tests\Concerns\Builder;

use Tests\Fixtures\InteractsWithTextImplementation;

it('is immutable', function () {
    $original = new InteractsWithTextImplementation();

    $mutated = $original->text('Hello World');
    expect($mutated)->not->toBe($original);

    $mutated = $original->noText();
    expect($mutated)->not->toBe($original);
});

it('can set text', function () {
    $builder = new InteractsWithTextImplementation();

    $mutated = $builder->text('Hello World');
    expect($mutated->renderText())->toBe('Hello World');
});

it('can set text to null', function () {
    $builder = new InteractsWithTextImplementation();

    $mutated = $builder->text('Hello World');
    expect($mutated->renderText())->toBe('Hello World');

    $mutated = $mutated->noText();
    expect($mutated->renderText())->toBe(null);
});

it('can check if text is set', function () {
    $builder = new InteractsWithTextImplementation();

    expect($builder->hasText())->toBeFalse();

    $mutated = $builder->text('Hello World');
    expect($mutated->hasText())->toBeTrue();

    $mutated = $mutated->noText();
    expect($mutated->hasText())->toBeFalse();
});
