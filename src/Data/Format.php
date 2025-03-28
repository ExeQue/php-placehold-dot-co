<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Data;

enum Format: string
{
    case PNG = 'png';

    case JPEG = 'jpeg';

    case GIF = 'gif';

    case WEBP = 'webp';

    case SVG = 'svg';

    case AVIF = 'avif';

    public function mime(): string
    {
        return match ($this) {
            self::PNG => 'image/png',
            self::JPEG => 'image/jpeg',
            self::GIF => 'image/gif',
            self::WEBP => 'image/webp',
            self::SVG => 'image/svg+xml',
            self::AVIF => 'image/avif',
        };
    }
}
