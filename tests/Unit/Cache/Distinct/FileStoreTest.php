<?php

declare(strict_types=1);

namespace Tests\Cache\Distinct;

use ExeQue\PlaceholdDotCo\Cache\Distinct\FileStore;
use Psr\SimpleCache\CacheInterface;
use Tests\TestCase;

covers(FileStore::class);

arch()->expect(FileStore::class)->toImplement(CacheInterface::class);

it('stores as files on disk', function () {
    $store = new FileStore($this->cacheDirectory);

    $expected = $this->cacheDirectory . '/placehold.co/' . hash('sha256', 'foo');

    $store->set('foo', 'bar');

    expect($this->filesystem->exists($expected))->toBeTrue();
})->skipOnCi();

it('stores as files on disk with a custom prefix', function () {
    $store = new FileStore($this->cacheDirectory, 'custom-prefix');

    $expected = $this->cacheDirectory . '/custom-prefix/' . hash('sha256', 'foo');

    $store->set('foo', 'bar');

    expect($this->filesystem->exists($expected))->toBeTrue();
})->skipOnCi();

it('returns default if the cache target is not a file', function () {
    $store = new FileStore($this->cacheDirectory);

    $path = $this->cacheDirectory . '/placehold.co/' . hash('sha256', 'foo');
    $default = random_bytes(16);

    $store->set('foo', 'bar');

    $this->filesystem->remove($path);
    $this->filesystem->mkdir($path);

    expect($store->get('foo', $default))->toBe($default);
})->skipOnCi();

it('should not clear non-file cache items', function () {
    $store = new FileStore($this->cacheDirectory);

    $path = $this->cacheDirectory . '/placehold.co/' . hash('sha256', 'foo');

    $this->filesystem->mkdir($path);

    $store->clear();

    expect($this->filesystem->exists($path))->toBeTrue();
})->skipOnCi();

it('returns false when checking for existence of a non-file cache item', function () {
    $store = new FileStore($this->cacheDirectory);

    $path = $this->cacheDirectory . '/placehold.co/' . hash('sha256', 'foo');

    $this->filesystem->mkdir($path);

    expect($store->has('foo'))->toBeFalse();
})->skipOnCi();

storeTests(fn (TestCase $case) => new FileStore($case->cacheDirectory), true);
