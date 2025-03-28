<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

trait ActsAsImmutable
{
    private function mutate(?callable $callback = null): static
    {
        $that = clone $this;

        if ($callback === null) {
            return $that;
        }

        $out = $callback($that);

        if ($out instanceof self) {
            return $out;
        }

        return $that;
    }
}
