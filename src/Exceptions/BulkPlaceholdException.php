<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Exceptions;

use ExeQue\PlaceholdDotCo\Exceptions\Contracts\PlaceholdException;
use RuntimeException;
use Webmozart\Assert\Assert;

class BulkPlaceholdException extends RuntimeException implements PlaceholdException
{
    /**
     * @param PlaceholdException[] $exceptions
     */
    public function __construct(
        public readonly array $exceptions
    ) {
        Assert::allIsInstanceOf($exceptions, PlaceholdException::class);

        parent::__construct($this->createMessage(), 0, reset($exceptions));
    }

    private function createMessage(): string
    {
        $messages = [];

        foreach ($this->exceptions as $exception) {
            $messages[] = $exception->getMessage();
        }

        $base = array_shift($messages);

        if ($messages === []) {
            return $base;
        }

        return $base . ' (and ' . count($messages) . ' more)';
    }
}
