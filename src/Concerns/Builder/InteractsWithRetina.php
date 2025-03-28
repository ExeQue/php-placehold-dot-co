<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Exceptions\PlaceholdException;

trait InteractsWithRetina
{
    use ActsAsImmutable;

    private array $supportedRetina = [
        Format::PNG,
        Format::JPEG,
        Format::GIF,
        Format::WEBP,
        Format::AVIF,
    ];

    abstract public function getFormat(): Format;

    private ?string $retina = null;

    public function x1(): static
    {
        return $this->mutate(function (self $builder) {
            $builder->retina = null;
        });
    }

    public function x2(): static
    {
        return $this->mutate(function (self $builder) {
            $builder->retina = '2x';
        });
    }

    public function x3(): static
    {
        return $this->mutate(function (self $builder) {
            $builder->retina = '3x';
        });
    }

    private function renderRetina(): string
    {
        if ($this->retina === null) {
            return '';
        }

        if (in_array($this->getFormat(), $this->supportedRetina, true) === false) {
            throw new PlaceholdException('Retina is not supported for this format: ' . $this->getFormat()->value);
        }

        return "@$this->retina";
    }
}
