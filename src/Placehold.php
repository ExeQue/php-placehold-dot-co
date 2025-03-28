<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo;

use ExeQue\PlaceholdDotCo\Data\Image;
use Generator;
use Webmozart\Assert\Assert;

class Placehold
{
    public function __construct(
        private Client $client = new Client(),
    ) {
    }

    /**
     * Returns a new instance of the image builder.
     * Note: The builder is immutable.
     */
    public function builder(): Builder
    {
        return new Builder($this->client);
    }

    /**
     * @return Generator<int|string, Image>
     */
    public function batch(iterable|Builder $builders, int $concurrent = 10): Generator
    {
        if ($builders instanceof Builder) {
            $builders = [$builders];
        }

        $uris = [];

        /** @var Builder $builder */
        foreach ($builders as $index => $builder) {
            Assert::isInstanceOf($builder, Builder::class);

            $uris[$index] = $builder->uri();
        }

        $streams = $this->client->getAsync($uris, $concurrent);

        foreach ($streams as $index => $stream) {
            $builder = $builders[$index];
            $uri = $uris[$index];

            yield $index => new Image(
                (string) $uri,
                $builder->getFormat(),
                $stream,
            );
        }
    }
}
