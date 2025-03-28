<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Data;

use RuntimeException;
use Webmozart\Assert\Assert;

/**
 * @property-read string $contents
 */
class Image
{
    public readonly string $uri;

    public readonly string $mime;

    /** @var ?resource */
    private mixed $resource;

    public readonly int $size;

    public function __construct(
        string $uri,
        Format $format,
        mixed $resource,
    ) {
        Assert::resource($resource);

        $this->uri = $uri;
        $this->mime = $format->mime();
        $this->resource = $resource;
        $this->size = fstat($this->resource)['size'];
    }

    public function __destruct()
    {
        if ($this->resource) {
            fclose($this->resource);
        }
    }

    /**
     * @return ?resource
     */
    public function detach(): mixed
    {
        if ($this->resource === null) {
            return null;
        }

        $resource = $this->resource();

        $this->resource = null;

        return $resource;
    }

    private function resource(): mixed
    {
        if (! $this->resource) {
            throw new RuntimeException('The image resource has been detached.');
        }

        return $this->resource;
    }

    public function __get(string $name)
    {
        if ($name === 'contents') {
            rewind($this->resource());

            return stream_get_contents($this->resource());
        }

        throw new RuntimeException(sprintf('Undefined property: %s::$%s', __CLASS__, $name));
    }
}
