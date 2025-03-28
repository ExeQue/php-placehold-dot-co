<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

use ExeQue\PlaceholdDotCo\Data\Font;

trait InteractsWithFont
{
    use ActsAsImmutable;

    private ?Font $font = null;

    abstract private function hasText(): bool;

    public function font(Font $font): static
    {
        return $this->mutate(function (self $builder) use ($font) {
            $builder->font = $font;
        });
    }

    public function lato(): static
    {
        return $this->font(Font::Lato);
    }

    public function lora(): static
    {
        return $this->font(Font::Lora);
    }

    public function montserrat(): static
    {
        return $this->font(Font::Montserrat);
    }

    public function notoSans(): static
    {
        return $this->font(Font::NotoSans);
    }

    public function openSans(): static
    {
        return $this->font(Font::OpenSans);
    }

    public function oswald(): static
    {
        return $this->font(Font::Oswald);
    }

    public function playfairDisplay(): static
    {
        return $this->font(Font::PlayfairDisplay);
    }

    public function poppins(): static
    {
        return $this->font(Font::Poppins);
    }

    public function ptSans(): static
    {
        return $this->font(Font::PTSans);
    }

    public function raleway(): static
    {
        return $this->font(Font::Raleway);
    }

    public function roboto(): static
    {
        return $this->font(Font::Roboto);
    }

    public function sourceSansPro(): static
    {
        return $this->font(Font::SourceSansPro);
    }

    private function renderFont(): ?string
    {
        if ($this->hasText() === false) {
            return null;
        }

        return $this->font?->value;
    }
}
