<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo;

use ExeQue\PlaceholdDotCo\Cache\Contracts\ImageStore as ImageStoreContract;
use ExeQue\PlaceholdDotCo\Cache\ImageStore;
use ExeQue\PlaceholdDotCo\Exceptions\BulkPlaceholdException;
use ExeQue\PlaceholdDotCo\Exceptions\PlaceholdException;
use Generator;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Webmozart\Assert\Assert;

class Client
{
    private ImageStoreContract $store;

    public function __construct(
        private ClientInterface $client = new Guzzle(),
        ?ImageStoreContract $imageStore = null
    ) {
        $this->store = $imageStore ?? ImageStore::temp();
    }

    /**
     * @return resource
     */
    public function get(UriInterface $uri): mixed
    {
        return $this->store->remember(
            (string)$uri,
            fn () => $this->wrap(
                fn () => $this->client->request('GET', $uri, $this->options())->getBody()->detach(),
            )
        );
    }

    /**
     * @return Generator<int|string, resource>
     */
    public function getAsync(
        array $uris,
        int $concurrent = 10 //@pest-mutate-ignore
    ): Generator {
        Assert::allIsInstanceOf($uris, UriInterface::class);

        $deduplication = [];
        $uniqueUris = array_unique($uris);

        foreach ($uniqueUris as $uri) {
            $deduplication[(string)$uri] = [];
        }

        foreach ($uris as $index => $uri) {
            $deduplication[(string)$uri][] = $index;
        }

        $requests = function () use ($uniqueUris) {
            foreach ($uniqueUris as $index => $uri) {
                if ($this->store->has((string)$uri)) {
                    yield $index => fn () => new FulfilledPromise(new Response(body: $this->store->get((string)$uri)));
                } else {
                    yield $index => fn () => $this->client->requestAsync('GET', $uri, $this->options());
                }
            }
        };

        /** @var resource[] $streams */
        $streams = [];
        $errors = [];

        $pool = new Pool($this->client, $requests(), [
            'concurrency' => $concurrent, //@pest-mutate-ignore
            'fulfilled' => function (ResponseInterface $response, mixed $index, mixed $promise) use (
                $deduplication,
                $uris,
                &$streams
            ) {
                $stream = $response->getBody()->detach();
                $uri = (string)$uris[$index];

                foreach ($deduplication[$uri] as $subIndex) {
                    $subStream = fopen('php://temp', 'wrb+');

                    stream_copy_to_stream($stream, $subStream);
                    rewind($stream);

                    $streams[$subIndex] = $subStream;

                    $this->store->set((string)$uris[$subIndex], $subStream);
                }
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
