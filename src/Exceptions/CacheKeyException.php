<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Exceptions;

use ExeQue\PlaceholdDotCo\Exceptions\Contracts\PlaceholdException;
use InvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

class CacheKeyException extends InvalidArgumentException implements PsrInvalidArgumentException, PlaceholdException
{
}
