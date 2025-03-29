<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use ExeQue\PlaceholdDotCo\Cache\Contracts\ImageStore as ImageCacheContract;
use ExeQue\PlaceholdDotCo\Cache\Distinct\ArrayStore;
use ExeQue\PlaceholdDotCo\Cache\Distinct\FileStore;
use ExeQue\PlaceholdDotCo\Cache\Distinct\NullStore;
use ExeQue\PlaceholdDotCo\Cache\Distinct\TempStore;
use Psr\SimpleCache\CacheInterface;
use Webmozart\Assert\Assert;

class ImageStore implements ImageCacheContract
{
    public function __construct(
        private CacheInterface $cache,
        private DateInterval $defaultTtl = new DateInterval('PT1H'),
        private string $prefix = 'placehold.co',
    ) {
    }

    /**
     * @return resource
     */
    public function get(string $uri, ?callable $default = null): mixed
    {
        $key = $this->generateKey($uri);

        $data = $this->cache->get($key);

        if ($data === null) {
            $default ??= static fn () => null;

            return $default();
        }

        $stream = fopen('php://temp', 'wrb+');
        fwrite($stream, $data);
        rewind($stream);

        return $stream;
    }

    public function set(string $uri, mixed $raw, DateInterval|DateTimeInterface|null $ttl = null): bool
    {
        if (is_string($raw)) {
            $stream = fopen('php://temp', 'wrb+');
            fwrite($stream, $raw);
            rewind($stream);

            $raw = $stream;
        }

        Assert::resource($raw);

        $ttl ??= $this->defaultTtl;
        if (($ttl instanceof DateTimeInterface) && $diff = (new DateTime())->diff($ttl)) {
            $ttl = $diff;
        }

        $key = $this->generateKey($uri);

        rewind($raw);

        $set = $this->cache->set($key, stream_get_contents($raw), $ttl);

        rewind($raw);

        return $set;
    }

    public function remember(string $uri, callable $callback, DateInterval|DateTimeInterface|null $ttl = null): mixed
    {
        $value = $this->get($uri);

        if ($value !== null) {
            return $value;
        }

        $raw = $callback();

        $this->set($uri, $raw, $ttl);

        return $raw;
    }

    public function delete(string $uri): bool
    {
        return $this->cache->delete(
            $this->generateKey($uri)
        );
    }

    public function has(string $uri): bool
    {
        return $this->cache->has(
            $this->generateKey($uri)
        );
    }

    private function generateKey(string $uri): string
    {
        return "$this->prefix:$uri";
    }

    /**
     * @codeCoverageIgnore
     */
    public static function temp(): static
    {
        return new static(
            new TempStore()
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public static function file(string $directory, string $prefix = 'placehold.co'): static
    {
        return new static(
            new FileStore($directory, $prefix)
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public static function null(): static
    {
        return new static(
            new NullStore()
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public static function memory(): static
    {
        return new static(
            new ArrayStore()
        );
    }
}
