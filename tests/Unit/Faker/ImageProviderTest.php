<?php

declare(strict_types=1);

use ExeQue\PlaceholdDotCo\Data\Font;
use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Faker\ImageProvider;
use Pest\Plugins\Environment;

it('can create an image url', function () {
    /** @var \Faker\Generator|ImageProvider $faker */
    $provider = new ImageProvider();

    $expected = 'https://placehold.co/1234x4321/transparent/black.jpeg?text=Hello+World&font=lato';

    $actual = $provider->placeholdCoUrl(
        1234,
        4321,
        'Hello World',
        Format::JPEG,
        'transparent',
        'black',
        Font::Lato
    );

    expect($actual)->toBe($expected);
})->skip(Environment::name() === Environment::CI, 'Skip test on CI');

it('can create an image', function () {
    /** @var \Faker\Generator|ImageProvider $faker */
    $provider = new ImageProvider();

    $actual = $provider->placeholdCoImage(
        1234,
        4321,
        'Hello World',
        Format::PNG,
        'transparent',
        'black',
        Font::Lato
    );

    expect($actual)->toMatch('/^.PNG/');
})->skip(Environment::name() === Environment::CI, 'Skip test on CI');

it('can create an image resource', function () {
    /** @var \Faker\Generator|ImageProvider $faker */
    $provider = new ImageProvider();

    $actual = $provider->placeholdCoResource(
        1234,
        4321,
        'Hello World',
        Format::PNG,
        'transparent',
        'black',
        Font::Lato
    );

    expect($actual)->toBeResource();
})->skip(Environment::name() === Environment::CI, 'Skip test on CI');
