<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo;

use ExeQue\PlaceholdDotCo\Exceptions\BulkPlaceholdException;
use ExeQue\PlaceholdDotCo\Exceptions\PlaceholdException;
use Generator;
use GuzzleHttp\Pool;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Webmozart\Assert\Assert;

class Client
{
    public function __construct(
        private ClientInterface $client = new \GuzzleHttp\Client(),
    ) {
    }

    /**
     * @return resource
     */
    public function get(UriInterface $uri): mixed
    {
        return $this->wrap(
            fn () => $this->client->request('GET', $uri, $this->options())->getBody()->detach(),
        );
    }

    /**
     * @return Generator<int|string, resource>
     */
    public function getAsync(
        iterable $uris,
        int $concurrent = 10 //@pest-mutate-ignore
    ): Generator {
        Assert::allIsInstanceOf($uris, UriInterface::class);

        $requests = function () use ($uris) {
            foreach ($uris as $index => $uri) {
                yield $index => fn () => $this->client->requestAsync('GET', $uri, $this->options());
            }
        };

        /** @var resource[] $streams */
        $streams = [];
        $errors = [];

        $pool = new Pool($this->client, $requests(), [
            'concurrency' => $concurrent, //@pest-mutate-ignore
            'fulfilled' => function (ResponseInterface $response, mixed $index, mixed $promise) use (&$streams) {
                $streams[$index] = $response->getBody()->detach();
            },
            'rejected' => function (RequestExceptionInterface $exception, mixed $index) use (&$errors) {
                $errors[$index] = new PlaceholdException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception,
                );
            }
        ]);

        $pool->promise()->wait();

        if ($errors !== []) {
            throw new BulkPlaceholdException($errors);
        }

        foreach ($streams as $index => $stream) {
            yield $index => $stream;
        }
    }

    private function options(): array
    {
        return [
            RequestOptions::HEADERS => [
                'User-Agent' => 'ExeQue/PlaceholdDotCo 1.0',
            ],
        ];
    }

    private function wrap(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (ClientExceptionInterface $exception) {
            throw new PlaceholdException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception,
            );
        }
    }
}
