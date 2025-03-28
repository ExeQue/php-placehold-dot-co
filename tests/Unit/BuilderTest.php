<?php

declare(strict_types=1);

namespace Tests;

use ExeQue\PlaceholdDotCo\Builder;
use ExeQue\PlaceholdDotCo\Client;
use Mockery\MockInterface;

covers(Builder::class);

it('creates uri', function () {
    $expected = 'https://placehold.co/800x600@2x/black/white.png?text=foobar&font=lato';

    $builder = new Builder(mock(Client::class));

    $builder = $builder
        ->size(800, 600)
        ->lato()
        ->text('foobar')
        ->color('black', 'white')
        ->x2()
        ->png();

    expect((string)$builder->uri())->toBe($expected);
});

it('renders an image', function () {
    $stream = fopen('php://temp', 'rb+');
    fwrite($stream, 'foobar');

    $builder = new Builder(
        mock(
            Client::class,
            fn (MockInterface $mock) => $mock->expects('get')->andReturn($stream)
        )
    );

    $expectedUri = 'https://placehold.co/800x600@2x/black/white.png?text=foobar&font=lato';

    $image = $builder
        ->size(800, 600)
        ->lato()
        ->text('foobar')
        ->color('black', 'white')
        ->x2()
        ->png()
        ->get();

    expect($image->uri)->toBe($expectedUri)
        ->and($image->mime)->toBe('image/png')
        ->and($image->size)->toBe(6)
        ->and($image->contents)->toBe('foobar');
});

it('removes double slashes from the uri', function () {
    $expected = 'https://placehold.co/300x300.png';

    $builder = new Builder(mock(Client::class));

    expect((string)$builder->uri())->toBe($expected);
});
