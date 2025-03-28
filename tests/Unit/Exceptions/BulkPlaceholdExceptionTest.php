<?php
declare(strict_types=1);

namespace Tests\Exceptions;

use ExeQue\PlaceholdDotCo\Exceptions\BulkPlaceholdException;
use ExeQue\PlaceholdDotCo\Exceptions\PlaceholdException;

test('message should be the message of the first exception with remaining count if greater than one', function () {
    $firstException = new PlaceholdException('First exception');
    $secondException = new PlaceholdException('Second exception');
    $thirdException = new PlaceholdException('Third exception');

    $bulkPlaceholdException = new BulkPlaceholdException([
        $firstException,
        $secondException,
        $thirdException,
    ]);

    expect($bulkPlaceholdException->getMessage())->toBe('First exception (and 2 more)');
});

test('message should be the message of the first exception if only one', function () {
    $firstException = new PlaceholdException('First exception');

    $bulkPlaceholdException = new BulkPlaceholdException([
        $firstException,
    ]);

    expect($bulkPlaceholdException->getMessage())->toBe('First exception');
});

it('should retain indices', function () {
    $firstException = new PlaceholdException('First exception');
    $secondException = new PlaceholdException('Second exception');
    $thirdException = new PlaceholdException('Third exception');

    $bulkPlaceholdException = new BulkPlaceholdException([
        'first' => $firstException,
        'second' => $secondException,
        'third' => $thirdException,
    ]);

    expect($bulkPlaceholdException->exceptions)->toHaveCount(3)
        ->and($bulkPlaceholdException->exceptions['first'])->toBe($firstException)
        ->and($bulkPlaceholdException->exceptions['second'])->toBe($secondException)
        ->and($bulkPlaceholdException->exceptions['third'])->toBe($thirdException);
});
