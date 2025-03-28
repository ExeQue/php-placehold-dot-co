<?php

declare(strict_types=1);

use ExeQue\PlaceholdDotCo\Builder;
use ExeQue\PlaceholdDotCo\Client;
use ExeQue\PlaceholdDotCo\Data\Image;
use ExeQue\PlaceholdDotCo\Placehold;
use Mockery\MockInterface;

covers(Placehold::class);

it('can create a new instance', function () {
    $placehold = new Placehold();

    expect($placehold)->toBeInstanceOf(Placehold::class);
});

it('can provide a builder', function () {
    $placehold = new Placehold();

    $builder = $placehold->builder();

    expect($builder)->toBeInstanceOf(Builder::class);
});

it('can create a batch of images', function () {
    $placehold = new Placehold(
        mock(Client::class, function (MockInterface $mock) {
            $mock->allows('getAsync')
                ->andReturnUsing(function (array $uris) {
                    foreach ($uris as $index => $uri) {
                        $stream = fopen('php://temp', 'rb+');
                        fwrite($stream, 'foobar');

                        yield $index => $stream;
                    }
                });
        })
    );

    $builder = $placehold->builder();

    $images = [
        'first' => $builder->svg(),
        'second' => $builder->avif(),
    ];

    $generator = $placehold->batch($images);

    expect($generator)->toBeInstanceOf(Generator::class);

    /** @var Image[] $images */
    $images = iterator_to_array($generator);

    expect($images)->each->toBeInstanceOf(Image::class)
        ->and($images)->toHaveCount(2)
        ->and($images)->toHaveKeys(['first', 'second'])
        ->and($images['first']->uri)->toBe('https://placehold.co/300x300.svg')
        ->and($images['first']->mime)->toBe('image/svg+xml')
        ->and($images['first']->contents)->toBe('foobar')
        ->and($images['first']->size)->toBe(6)
        ->and($images['second']->uri)->toBe('https://placehold.co/300x300.avif')
        ->and($images['second']->mime)->toBe('image/avif')
        ->and($images['second']->contents)->toBe('foobar')
        ->and($images['second']->size)->toBe(6);
});

it('allows for batching with a single builder', function () {
    $placehold = new Placehold(
        mock(Client::class, function (MockInterface $mock) {
            $mock->allows('getAsync')
                ->andReturnUsing(function (array $uris) {
                    foreach ($uris as $index => $uri) {
                        $stream = fopen('php://temp', 'rb+');
                        fwrite($stream, 'foobar');

                        yield $index => $stream;
                    }
                });
        })
    );

    $builder = $placehold->builder();

    $generator = $placehold->batch($builder);

    expect($generator)->toBeInstanceOf(Generator::class);

    /** @var Image[] $images */
    $images = iterator_to_array($generator);

    expect($images)->each->toBeInstanceOf(Image::class)
        ->and($images)->toHaveCount(1)
        ->and($images[0]->uri)->toBe('https://placehold.co/300x300.png')
        ->and($images[0]->mime)->toBe('image/png')
        ->and($images[0]->contents)->toBe('foobar')
        ->and($images[0]->size)->toBe(6);
});

it('should throw an error if batching with a non-builder', function () {
    $placehold = new Placehold();

    $generator = $placehold->batch([
        'not a builder'
    ]);

    expect(fn () => iterator_to_array($generator))->toThrow(
        InvalidArgumentException::class,
        'Expected an instance of ExeQue\PlaceholdDotCo\Builder.'
    );
});
