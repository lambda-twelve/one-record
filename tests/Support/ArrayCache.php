<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Support;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * A PSR-16 cache in an array, ignoring TTLs, counting writes.
 */
final class ArrayCache implements CacheInterface
{
    /** @var array<string, mixed> */
    public array $items = [];

    public int $writes = 0;

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->items[$key] = $value;
        $this->writes++;

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->items[$key]);

        return true;
    }

    public function clear(): bool
    {
        $this->items = [];

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = $this->get($key, $default);
        }

        return $out;
    }

    /**
     * @param iterable<mixed, mixed> $values
     */
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set(\is_string($key) ? $key : (string) (\is_scalar($key) ? $key : ''), $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->items);
    }
}
