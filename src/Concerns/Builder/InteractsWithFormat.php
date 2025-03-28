<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

use ExeQue\PlaceholdDotCo\Data\Format;

trait InteractsWithFormat
{
    use ActsAsImmutable;

    private Format $format = Format::PNG;

    public function format(Format $format): static
    {
        return $this->mutate(function (self $builder) use ($format) {
            $builder->format = $format;
        });
    }

    public function png(): static
    {
        return $this->format(Format::PNG);
    }

    public function jpeg(): static
    {
        return $this->format(Format::JPEG);
    }

    public function webp(): static
    {
        return $this->format(Format::WEBP);
    }

    public function svg(): static
    {
        return $this->format(Format::SVG);
    }

    public function gif(): static
    {
        return $this->format(Format::GIF);
    }

    public function avif(): static
    {
        return $this->format(Format::AVIF);
    }

    private function renderFormat(): string
    {
        return $this->format->value;
    }

    public function getFormat(): Format
    {
        return $this->format;
    }
}
