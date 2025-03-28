<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Concerns\Builder;

trait IsConditional
{
    use ActsAsImmutable;

    public function when(bool|callable $condition, callable $callback): static
    {
        if (is_bool($condition)) {
            $condition = static fn () => $condition;
        }

        if ($condition()) {
            return $this->mutate($callback);
        }

        return $this;
    }
}
