<?php

declare(strict_types=1);

namespace Tests\Data;

use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Data\Image;
use InvalidArgumentException;
use RuntimeException;
use stdClass;

it('does not allow non-resource', function () {
    $inputs = [
        'string' => ['string'],
        'int' => [1],
        'float' => [1.0],
        'bool' => [true],
        'array' => [[]],
        'object' => [new stdClass()],
    ];

    foreach ($inputs as $input) {
        expect(fn () => new Image('foobar', Format::AVIF, $input))->toThrow(
            InvalidArgumentException::class,
            'Expected a resource.'
        );
    }
});

it('automatically rewinds the stream', function () {
    $stream = fopen('php://temp', 'rb+');
    fwrite($stream, 'foobar');

    $image = new Image('foobar', Format::AVIF, $stream);

    expect($image->contents)->toBe('foobar')
        ->and($image->contents)->toBe('foobar');
});

it('closes the stream on destruct', function () {
    $stream = fopen('php://temp', 'rb+');
    fwrite($stream, 'foobar');

    $image = new Image('foobar', Format::AVIF, $stream);

    unset($image);

    expect(is_resource($stream))->toBeFalse();
});

it('can detach the stream', function () {
    $stream = fopen('php://temp', 'rb+');
    fwrite($stream, 'foobar');

    $image = new Image('foobar', Format::AVIF, $stream);

    expect($image->detach())->toBe($stream)
        ->and($image->detach())->toBeNull();
});

it('cannot read contents after detaching', function () {
    $stream = fopen('php://temp', 'rb+');
    fwrite($stream, 'foobar');

    $image = new Image('foobar', Format::AVIF, $stream);

    $image->detach();

    expect(fn () => $image->contents)->toThrow(
        RuntimeException::class,
        'The image resource has been detached.'
    );
});

it('sets the mime to match the format', function () {
    foreach (Format::cases() as $format) {
        $stream = fopen('php://temp', 'rb+');
        fwrite($stream, 'foobar');

        $image = new Image('foobar', $format, $stream);

        expect($image->mime)->toBe($format->mime());
    }
});
