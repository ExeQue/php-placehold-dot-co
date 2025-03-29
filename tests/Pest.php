<?php

use ExeQue\PlaceholdDotCo\Exceptions\CacheKeyException;
use Psr\SimpleCache\CacheInterface;

pest()->extend(Tests\TestCase::class);

function storeTests(callable $storeFactory, bool $skipOnCi = false): void
{
    $tests = [];

    $tests[] = it('can store and retrieve data', function (mixed $input) use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', $input);

        $actual = $store->get('foo');

        expect($actual)->toEqual($input)
            ->and(get_debug_type($actual))->toBe(get_debug_type($input));
    })->with([
        'string'  => [
            'input' => 'value',
        ],
        'array'   => [
            'input' => ['value'],
        ],
        'object'  => [
            'input' => new stdClass(),
        ],
        'integer' => [
            'input' => 123,
        ],
        'float'   => [
            'input' => 123.456,
        ],
    ]);

    $tests[] = it('returns default value if store misses', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $default = random_bytes(16);

        expect($store->get('foo'))->toBeNull()
            ->and($store->get('foo', $default))->toEqual($default);
    });

    $tests[] = it('can forget', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', 'value');

        expect($store->get('foo'))->toEqual('value');

        $forgottenOnce = $store->delete('foo');
        expect($store->get('foo'))->toBeNull();
        $forgottenTwice = $store->delete('foo');

        expect($forgottenOnce)->toBeTrue()
            ->and($forgottenTwice)->toBeFalse();
    });

    $tests[] = it('can clear', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', 'foo:value');
        $store->set('bar', 'bar:value');

        expect($store->get('foo'))->toEqual('foo:value')
            ->and($store->get('bar'))->toEqual('bar:value');

        $store->clear();

        expect($store->get('foo'))->toBeNull()
            ->and($store->get('bar'))->toBeNull();
    });

    $tests[] = it('can retrieve multiple', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', 'foo:value');
        $store->set('bar', 'bar:value');

        expect($store->getMultiple(['foo', 'bar']))->toEqual([
            'foo' => 'foo:value',
            'bar' => 'bar:value',
        ]);
    });

    $tests[] = it('returns default value if store misses on multiple', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $default = random_bytes(16);

        $store->set('foo', 'foo:value');

        expect($store->getMultiple(['foo', 'bar'], $default))->toEqual([
            'foo' => 'foo:value',
            'bar' => $default,
        ]);
    });

    $tests[] = it('can store multiple', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->setMultiple([
            'foo' => 'foo:value',
            'bar' => 'bar:value',
        ]);

        expect($store->get('foo'))->toEqual('foo:value')
            ->and($store->get('bar'))->toEqual('bar:value');
    });

    $tests[] = it('can forget multiple', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', 'foo:value');
        $store->set('bar', 'bar:value');

        expect($store->get('foo'))->toEqual('foo:value')
            ->and($store->get('bar'))->toEqual('bar:value');

        $store->deleteMultiple(['foo', 'bar']);

        expect($store->get('foo'))->toBeNull()
            ->and($store->get('bar'))->toBeNull();
    });

    $tests[] = it('can check if key exists', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', 'value');

        expect($store->has('foo'))->toBeTrue()
            ->and($store->has('bar'))->toBeFalse();
    });

    $tests[] = it('does not allow invalid keys', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        expect(fn () => $store->get(''))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->set('', 'value'))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->delete(''))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->has(''))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->getMultiple(['']))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->getMultiple([[]]))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->setMultiple(['' => 'value']))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->setMultiple(['value']))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->deleteMultiple(['']))->toThrow(CacheKeyException::class)
            ->and(fn () => $store->deleteMultiple([[]]))->toThrow(CacheKeyException::class);
    });

    $tests[] = it('should not return expired items', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', 'value', new DateInterval('PT0S'));

        sleep(1);

        expect($store->has('foo'))->toBeFalse();
        expect($store->get('foo'))->toBeNull();
    });

    $tests[] = it('works with integer ttl', function () use ($storeFactory) {
        /** @var CacheInterface $store */
        $store = $storeFactory($this);

        $store->set('foo', 'value', 0);

        sleep(1);

        expect($store->get('foo'))->toBeNull();
    });

    if($skipOnCi === true) {
        foreach ($tests as $test) {
            $test->skipOnCi();
        }
    }
}
