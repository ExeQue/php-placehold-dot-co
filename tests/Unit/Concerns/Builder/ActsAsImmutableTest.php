<?php

declare(strict_types=1);

namespace Tests\Concerns\Builder;

use Tests\Fixtures\ActsAsImmutableImplementation;

it('is immutable', function () {
    $original = new ActsAsImmutableImplementation();

    $mutated = $original->mutate();
    expect($mutated)->not->toBe($original);

    $mutated = $original->mutate(fn ($builder) => $builder);
    expect($mutated)->not->toBe($original);
});

it('returns a new instance if the callback is not provided', function () {
    $original = new ActsAsImmutableImplementation();

    $mutated = $original->mutate();
    expect($mutated)->not->toBe($original);
});

it('returns the output of the callback if it is an instance of itself', function () {
    $original = new ActsAsImmutableImplementation();

    $mutated = $original->mutate(fn ($builder) => $builder);
    expect($mutated)->not->toBe($original);

    $mutated = $original->mutate(fn ($builder) => new ActsAsImmutableImplementation());
    expect($mutated)->not->toBe($original);
});

it('returns the new instance if the output of the callback is not an instance of itself', function () {
    $original = new ActsAsImmutableImplementation();

    $mutated = $original->mutate(fn ($builder) => null);
    expect($mutated)->not->toBe($original);

    $mutated = $original->mutate(fn ($builder) => new class () {});
    expect($mutated)->not->toBe($original);
});
