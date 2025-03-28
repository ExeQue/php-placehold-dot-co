<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

trait InteractsWithText
{
    use ActsAsImmutable;

    private ?string $text = null;

    public function text(string $text): static
    {
        return $this->mutate(function (self $builder) use ($text) {
            $builder->text = $text;
        });
    }

    public function noText(): static
    {
        return $this->mutate(function (self $builder) {
            $builder->text = null;
        });
    }

    private function hasText(): bool
    {
        return $this->text !== null;
    }

    private function renderText(): ?string
    {
        return $this->text;
    }
}
