<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Faker;

use ExeQue\PlaceholdDotCo\Builder;
use ExeQue\PlaceholdDotCo\Data\Font;
use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Placehold;
use InvalidArgumentException;

/**
 * @codeCoverageIgnore
 */
class ImageProvider
{
    public function __construct(
        private Placehold $placehold = new Placehold(),
    ) {
    }

    public function placeholdCoUrl(
        int $width = 640,
        int $height = 480,
        ?string $text = null,
        string|Format $format = Format::PNG,
        ?string $background = null,
        ?string $foreground = null,
        string|Font $font = Font::Lora,
    ): string {
        $builder = $this->placeholdCoBuilder(
            $width,
            $height,
            $text,
            $format,
            $background,
            $foreground,
            $font,
        );

        return (string)$builder->uri();
    }

    public function placeholdCoImage(
        int $width = 640,
        int $height = 480,
        ?string $text = null,
        string|Format $format = Format::PNG,
        ?string $background = null,
        ?string $foreground = null,
        string|Font $font = Font::Lora,
    ): string {
        $builder = $this->placeholdCoBuilder(
            $width,
            $height,
            $text,
            $format,
            $background,
            $foreground,
            $font,
        );

        return $builder->get()->contents;
    }

    public function placeholdCoResource(
        int $width = 640,
        int $height = 480,
        ?string $text = null,
        string|Format $format = Format::PNG,
        ?string $background = null,
        ?string $foreground = null,
        string|Font $font = Font::Lora,
    ): mixed {
        $builder = $this->placeholdCoBuilder(
            $width,
            $height,
            $text,
            $format,
            $background,
            $foreground,
            $font,
        );

        return $builder->get()->detach();
    }

    private function placeholdCoBuilder(
        int $width = 640,
        int $height = 480,
        ?string $text = null,
        string|Format $format = Format::PNG,
        ?string $background = null,
        ?string $foreground = null,
        string|Font $font = Font::Lora,
    ): Builder {
        if (is_string($format)) {
            $format = Format::tryFrom($format) ?? throw new InvalidArgumentException('Invalid image format: ' . $format);
        }

        if (is_string($font)) {
            $font = Font::tryFrom($font) ?? throw new InvalidArgumentException('Invalid font: ' . $font);
        }

        return $this->placehold
            ->builder()
            ->size($width, $height)
            ->format($format)
            ->font($font)
            ->when($text !== null, fn (Builder $builder) => $builder->text($text))
            ->when($background !== null, fn (Builder $builder) => $builder->background($background))
            ->when($foreground !== null, fn (Builder $builder) => $builder->foreground($foreground));
    }
}
