<?php

declare(strict_types=1);

namespace Tests\Asserts;

use DateInterval;
use DateTime;
use DateTimeInterface;
use ExeQue\PlaceholdDotCo\Cache\Distinct\ArrayStore;
use ExeQue\PlaceholdDotCo\Cache\Distinct\Concerns\ResolvesTtl;
use Psr\SimpleCache\CacheInterface;

class AssertableCache implements CacheInterface
{
    use ResolvesTtl;

    private ArrayStore $store;

    /** @var DateTimeInterface[] */
    private array $expires = [];

    private array $hits = [];

    private array $misses = [];

    private array $writes = [];

    private array $deletes = [];

    public function __construct()
    {
        $this->store = new ArrayStore();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $out = $this->store->get($key, $default);

        if ($out !== $default) {
            $this->hits[] = $key;
        } else {
            $this->misses[] = $key;
        }

        return $out;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->expires[$key] = $this->resolveTtl($ttl);
        $this->writes[] = $key;

        return $this->store->set($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        unset($this->expires[$key]);

        $this->deletes[] = $key;

        return $this->store->delete($key);
    }

    public function clear(): bool
    {
        foreach ($this->expires as $key => $ttl) {
            $this->deletes[] = $key;
        }

        $this->expires = [];

        return $this->store->clear();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->expires)) {
                $this->hits[] = $key;
            } else {
                $this->misses[] = $key;
            }
        }

        return $this->store->getMultiple($keys, $default);
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->expires[$key] = $this->resolveTtl($ttl);

            $this->writes[] = $key;
        }

        return $this->store->setMultiple($values, $ttl);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            unset($this->expires[$key]);
        }

        return $this->store->deleteMultiple($keys);
    }

    public function has(string $key): bool
    {
        return $this->store->has($key);
    }

    public function assertIsExpired(string $key): static
    {
        $this->assertHas($key);

        expect($this->expires[$key]->format('U'))->toBeLessThanOrEqual((new DateTime())->format('U'), 'Cache key is not expired');

        return $this;
    }

    public function assertHas(string $key): static
    {
        expect($this->store->has($key))->toBeTrue('Cache key does not exist');

        return $this;
    }

    public function assertMisses(int $count): static
    {
        expect($this->misses)->toHaveCount($count, 'Cache misses do not match the expected count');

        return $this;
    }

    public function assertHits(int $count): static
    {
        expect($this->hits)->toHaveCount($count, 'Cache hits do not match the expected count');

        return $this;
    }

    public function assertWrites(int $count): static
    {
        expect($this->writes)->toHaveCount($count, 'Cache writes do not match the expected count');

        return $this;
    }

    public function assertDeletes(int $count): static
    {
        expect($this->deletes)->toHaveCount($count, 'Cache deletes do not match the expected count');

        return $this;
    }

    public function assertMissed(string $key, ?int $count = null): static
    {
        expect($this->misses)->toContain($key);

        if ($count !== null) {
            $counter = 0;
            foreach ($this->misses as $miss) {
                if ($miss === $key) {
                    $counter++;
                }
            }
            expect($counter)->toBe($count, 'Cache key was not missed the expected number of times');
        }

        return $this;
    }

    public function assertHit(string $key, ?int $count = null): static
    {
        expect($this->hits)->toContain($key);

        if ($count !== null) {
            $counter = 0;
            foreach ($this->hits as $hit) {
                if ($hit === $key) {
                    $counter++;
                }
            }
            expect($counter)->toBe($count, 'Cache key was not hit the expected number of times');
        }

        return $this;
    }

    public function assertWritten(string $key, ?int $count = null): static
    {
        expect($this->writes)->toContain($key);

        if ($count !== null) {
            $counter = 0;
            foreach ($this->writes as $write) {
                if ($write === $key) {
                    $counter++;
                }
            }
            expect($counter)->toBe($count, 'Cache key was not written the expected number of times');
        }

        return $this;
    }

    public function assertDeleted(string $key, ?int $count = null): static
    {
        expect($this->deletes)->toContain($key);

        if ($count !== null) {
            $counter = 0;
            foreach ($this->deletes as $delete) {
                if ($delete === $key) {
                    $counter++;
                }
            }
            expect($counter)->toBe($count, 'Cache key was not deleted the expected number of times');
        }

        return $this;
    }

    public function assertUsesPrefix(string $prefix): static
    {
        $prefixed = true;
        foreach ($this->expires as $key => $value) {
            if (str_starts_with($key, $prefix) === false) {
                $prefixed = false;
            }
        }

        expect($prefixed)->toBeTrue('Cache keys do not use the expected prefix');

        return $this;
    }

    public function assertSet(string $key, mixed $value = null, ?DateTimeInterface $ttl = null): static
    {
        expect($this->has($key))->toBeTrue('Cache key does not exist')
            ->when($value !== null, function () use ($value, $key) {
                expect($this->get($key))->toBe($value);
            })
            ->when($ttl !== null, function () use ($ttl, $key) {
                expect($this->expires[$key])->toBeInstanceOf(DateTimeInterface::class)
                    ->and($this->expires[$key]->format('U'))->toBe($ttl->format('U'));
            });

        return $this;
    }

    public function assertDoesntHave(string $string): static
    {
        expect($this->has($string))->toBeFalse('Cache key exists');

        return $this;
    }
}
