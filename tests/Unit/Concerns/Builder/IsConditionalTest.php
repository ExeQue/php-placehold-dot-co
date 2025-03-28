<?php

declare(strict_types=1);

use Tests\Fixtures\IsConditionalImplementation;

it('is immutable when true', function () {
    $original = new IsConditionalImplementation();

    $mutated = $original->when(true, fn () => null);
    expect($mutated)->not->toBe($original);

    $mutated = $original->when(false, fn () => null);
    expect($mutated)->toBe($original);
});

it('supports callable conditions', function () {
    $original = new IsConditionalImplementation();

    $mutated = $original->when(fn () => true, fn () => null);
    expect($mutated)->not->toBe($original);

    $mutated = $original->when(fn () => false, fn () => null);
    expect($mutated)->toBe($original);
});
