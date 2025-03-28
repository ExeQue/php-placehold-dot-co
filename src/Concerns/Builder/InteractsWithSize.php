<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

trait InteractsWithSize
{
    use ActsAsImmutable;

    private array $size = [
        'width' => 300,
        'height' => 300,
    ];

    public function height(int $height): static
    {
        return $this->mutate(function (self $builder) use ($height) {
            $builder->size['height'] = $height;
        });
    }

    public function width(int $width): static
    {
        return $this->mutate(function (self $builder) use ($width) {
            $builder->size['width'] = $width;
        });
    }

    public function size(int $width, int $height): static
    {
        return $this->width($width)->height($height);
    }

    public function square(int $size): static
    {
        return $this->size($size, $size);
    }

    public function landscape(): static
    {
        return $this->mutate(function (self $builder) {
            if ($builder->size['width'] < $builder->size['height']) {
                return $builder->size(
                    $this->size['height'],
                    $this->size['width'],
                );
            }

            return $builder;
        });
    }

    public function portrait(): static
    {
        return $this->mutate(function (self $builder) {
            if ($builder->size['width'] > $builder->size['height']) {
                return $builder->size(
                    $this->size['height'],
                    $this->size['width'],
                );
            }

            return $builder;
        });
    }

    private function renderSize(): string
    {
        return "{$this->size['width']}x{$this->size['height']}";
    }
}
