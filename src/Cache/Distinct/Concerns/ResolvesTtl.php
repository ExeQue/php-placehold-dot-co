<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns;

use DateInterval;
use DateTime;
use DateTimeInterface;

trait ResolvesTtl
{
    protected function resolveTtl(DateInterval|int|null $ttl = null): DateTime
    {
        $ttl ??= new DateInterval('P7D');

        if (is_int($ttl)) {
            $ttl = new DateInterval('PT' . $ttl . 'S');
        }

        return (new DateTime())->add($ttl);
    }

    protected function isExpired(DateTimeInterface $expires): bool
    {
        return (new DateTime())->format('U') > $expires->format('U');
    }
}
