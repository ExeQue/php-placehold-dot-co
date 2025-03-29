<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo;

use ExeQue\PlaceholdDotCo\Cache\Distinct\NullStore;
use ExeQue\PlaceholdDotCo\Cache\Distinct\TempStore;
use ExeQue\PlaceholdDotCo\Cache\ImageStore;
use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithColor;
use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithFont;
use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithFormat;
use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithRetina;
use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithSize;
use ExeQue\PlaceholdDotCo\Concerns\Builder\InteractsWithText;
use ExeQue\PlaceholdDotCo\Concerns\Builder\IsConditional;
use ExeQue\PlaceholdDotCo\Data\Image;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class Builder
{
    use InteractsWithColor;
    use InteractsWithFont;
    use InteractsWithFormat;
    use InteractsWithSize;
    use InteractsWithText;
    use InteractsWithRetina;

    use IsConditional;

    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function get(): Image
    {
        $uri = $this->uri();

        return new Image(
            (string)$uri,
            $this->format,
            $this->client->get($uri),
        );
    }

    public function uri(): UriInterface
    {
        return (new Uri())
            ->withScheme('https')
            ->withHost('placehold.co')
            ->withPath($this->renderPath())
            ->withQuery(http_build_query($this->renderQuery()));
    }

    public function renderPath(): string
    {
        $fragments = [
            'size' => $this->renderSize(),
            ...$this->renderColor(),
            'retina' => $this->renderRetina(),
        ];

        $path = '{size}{retina}/{background}/{foreground}';

        foreach ($fragments as $name => $fragment) {
            $path = str_replace("{{$name}}", $fragment, $path);
        }

        $path = preg_replace('~/+~', '/', $path);
        $path = trim($path, '/');
        $path .= ".{$this->renderFormat()}";

        return $path;
    }

    private function renderQuery(): array
    {
        return [
            'text' => $this->renderText(),
            'font' => $this->renderFont(),
        ];
    }
}
