<?php

declare(strict_types=1);

use ExeQue\PlaceholdDotCo\Cache\ImageStore;
use Tests\Asserts\AssertableCache;

covers(ImageStore::class);

it('prefixes cache', function () {
    $store = new ImageStore(
        cache: $cache = new AssertableCache(),
        prefix: $prefix = 'foobar.placeholder',
    );

    $store->set('key', 'value');

    $cache->assertUsesPrefix($prefix);
});

it('can accept both string and resources', function () {
    $store = new ImageStore(
        cache: new AssertableCache(),
    );

    $store->set('string', 'value');
    $store->set('resource', fopen('php://temp', 'rb+'));

    $this->expectNotToPerformAssertions();
});

it('outputs resources', function () {
    $store = new ImageStore(
        cache: new AssertableCache(),
    );

    $store->set('resource', fopen('php://temp', 'rb+'));

    expect($store->get('resource'))->toBeResource();
});

it('outputs default value of cache misses', function () {
    $store = new ImageStore(
        cache: $cache = new AssertableCache(),
    );

    expect($store->get('non-existing-key'))->toBeNull()
        ->and($store->get('non-existing-key', fn () => 'test'))->toBe('test');

    $cache
        ->assertMisses(2)
        ->assertMissed('placehold.co:non-existing-key', 2);
});

it('can change default ttl', function () {
    $store = new ImageStore(
        cache: $cache = new AssertableCache(),
        defaultTtl: new DateInterval('PT3H'),
    );

    $now = new DateTime();
    $future = $now->add(new DateInterval('PT3H'));

    $store->set('key', 'value');

    $cache->assertSet('placehold.co:key', 'value', $future);
});

it('can set ttl', function () {
    $store = new ImageStore(
        cache: $cache = new AssertableCache(),
    );

    $now = new DateTime();
    $future = $now->add(new DateInterval('PT3H'));

    $store->set('key', 'value', $future);

    $cache->assertSet('placehold.co:key', 'value', $future);
});

it('caches output when using remember', function () {
    $store = new ImageStore(
        cache: $cache = new AssertableCache(),
    );

    $actual1 = $store->remember('foobar', function () {
        return 'value';
    });

    $actual2 = stream_get_contents($store->get('foobar'));

    $cache->assertMissed('placehold.co:foobar', 1)
        ->assertWritten('placehold.co:foobar', 1)
        ->assertHit('placehold.co:foobar', 1);

    expect($actual1)->toBe('value')
        ->and($actual2)->toBe('value');

    $actual3 = stream_get_contents(
        $store->remember('foobar', function () {
            expect(false)->toBeTrue(); // this should not be called
        })
    );

    $cache->assertHit('placehold.co:foobar', 2)
        ->assertMissed('placehold.co:foobar', 1)
        ->assertWritten('placehold.co:foobar', 1);

    expect($actual3)->toBe('value');
});

it('can forget', function () {
    $store = new ImageStore(
        cache: $cache = new AssertableCache(),
    );

    $store->set('key', 'value');

    $store->delete('key');

    $cache->assertDeleted('placehold.co:key');
});

it('can check if key exists', function () {
    $store = new ImageStore(
        cache: $cache = new AssertableCache(),
    );

    $store->set('key', 'value');

    expect($store->has('key'))->toBeTrue()
        ->and($store->has('non-existing-key'))->toBeFalse();

    $cache->assertHas('placehold.co:key')
        ->assertDoesntHave('placehold.co:non-existing-key');
});
