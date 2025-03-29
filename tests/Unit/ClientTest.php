<?php

declare(strict_types=1);

namespace Tests;

use ExeQue\PlaceholdDotCo\Client;
use ExeQue\PlaceholdDotCo\Exceptions\BulkPlaceholdException;
use ExeQue\PlaceholdDotCo\Exceptions\PlaceholdException;
use Generator;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

covers(Client::class);

it('returns a resource using `get`', function () {
    $client = new Client(
        new Guzzle([
            'handler' => MockHandler::createWithMiddleware([
                new Response(200, body: 'foobar'),
            ]),
        ]),
    );

    $output = $client->get(new Uri('foo.bar'));

    $expected = 'foobar';

    $actual = stream_get_contents($output);

    expect($actual)->toBe($expected);
});

it('returns a generator with resources using `getAsync`', function () {
    $client = new Client(
        new Guzzle([
            'handler' => MockHandler::createWithMiddleware([
                new Response(200, body: 'foobar'),
                new Response(200, body: 'barfoo'),
            ]),
        ]),
    );

    $output = $client->getAsync([
        'first' => new Uri('foo.bar'),
        'second' => new Uri('bar.foo'),
    ]);

    expect($output)->toBeInstanceOf(Generator::class);

    $resources = iterator_to_array($output);

    expect($resources)->toHaveCount(2)
        ->and(stream_get_contents($resources['first']))->toBe('foobar')
        ->and(stream_get_contents($resources['second']))->toBe('barfoo');
});

it('handles errors gracefully using `get`', function () {
    $client = new Client(
        new Guzzle([
            'handler' => MockHandler::createWithMiddleware([
                new Response(400),
                new Response(500),
            ]),
        ]),
    );

    expect()
        ->and(fn () => $client->get(new Uri('foo.bar')))->toThrow(function (PlaceholdException $exception) {
            expect($exception->getPrevious())->toBeInstanceOf(ClientException::class);
        })
        ->and(fn () => $client->get(new Uri('foo.bar')))->toThrow(function (PlaceholdException $exception) {
            expect($exception->getPrevious())->toBeInstanceOf(ServerException::class);
        });
});

it('handles errors gracefully using `getAsync`', function () {
    $client = new Client(
        new Guzzle([
            'handler' => MockHandler::createWithMiddleware([
                new Response(400),
                new Response(500),
            ]),
        ]),
    );

    $generator = $client->getAsync([
        'first' => new Uri('foo.bar'),
        'second' => new Uri('bar.foo')
    ]);

    expect(fn () => iterator_to_array($generator))->toThrow(function (BulkPlaceholdException $exception) {
        expect($exception->exceptions['first']->getMessage())->toContain('Client error')
            ->and($exception->exceptions['first']->getPrevious())->toBeInstanceOf(ClientException::class)
            ->and($exception->exceptions['second']->getMessage())->toContain('Server error')
            ->and($exception->exceptions['second']->getPrevious())->toBeInstanceOf(ServerException::class);
    });
});

it('fails if given a non-uri using `getAsync`', function () {
    $client = new Client(
        new Guzzle([
            'handler' => MockHandler::createWithMiddleware([
                new Response(200, body: 'foobar'),
            ]),
        ]),
    );

    $generator = $client->getAsync(['foo.bar']);

    expect(fn () => iterator_to_array($generator))->toThrow(InvalidArgumentException::class);
});

it('sends the correct headers', function () {
    $client = new Client(
        new Guzzle([
            'handler' => function (RequestInterface $request, array $options) {
                expect($request->getHeaderLine('User-Agent'))->toStartWith('ExeQue/PlaceholdDotCo');

                return new Response(200);
            },
        ]),
    );

    $client->get(new Uri('foo.bar'));
});

it('caches responses', function () {
    $client = new Client(
        new Guzzle([
            'handler' => MockHandler::createWithMiddleware([
                new Response(200, body: 'foobar'),
            ]),
        ]),
    );

    $client->get(new Uri('foo.bar'));
    $client->get(new Uri('foo.bar'));

    $this->expectNotToPerformAssertions();
});

it('deduplicates requests when async', function () {
    $client = new Client(
        new Guzzle([
            'handler' => MockHandler::createWithMiddleware([
                new Response(200, body: 'foobar'),
            ]),
        ]),
    );

    $generator = $client->getAsync([
        'first' => new Uri('foo.bar'),
        'second' => new Uri('foo.bar'),
    ]);

    expect($generator)->toBeInstanceOf(Generator::class);

    $resources = iterator_to_array($generator);

    expect($resources)->toHaveCount(2)
        ->and(stream_get_contents($resources['first']))->toBe('foobar')
        ->and(stream_get_contents($resources['second']))->toBe('foobar');

    $generator = $client->getAsync([
        'first' => new Uri('foo.bar'),
        'second' => new Uri('foo.bar'),
    ]);

    expect($generator)->toBeInstanceOf(Generator::class);

    $resources = iterator_to_array($generator);

    expect($resources)->toHaveCount(2)
        ->and(stream_get_contents($resources['first']))->toBe('foobar')
        ->and(stream_get_contents($resources['second']))->toBe('foobar');
});
