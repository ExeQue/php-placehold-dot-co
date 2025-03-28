<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

trait InteractsWithColor
{
    use ActsAsImmutable;

    public array $color = [];

    public function color(string $background, string $foreground): static
    {
        return $this->mutate(function (self $builder) use ($background, $foreground) {
            $builder->color = [
                'background' => $background,
                'foreground' => $foreground,
            ];
        });
    }

    public function background(string $background): static
    {
        return $this->mutate(function (self $builder) use ($background) {
            $builder->color['background'] = $background;
        });
    }

    public function foreground(string $foreground): static
    {
        return $this->mutate(function (self $builder) use ($foreground) {
            if (isset($this->color['background']) === false) {
                $builder->color['background'] = 'white';
            }

            $builder->color['foreground'] = $foreground;
        });
    }

    private function renderColor(): array
    {
        return [
            'background' => $this->color['background'] ?? '',
            'foreground' => $this->color['foreground'] ?? '',
        ];
    }
}
