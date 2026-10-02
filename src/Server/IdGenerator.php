<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Model\Uuid;

/**
 * Ids for the resources the server creates itself (action requests, events,
 * objects posted by partners): random UUIDs from an injectable byte source so
 * tests can be deterministic.
 */
final class IdGenerator
{
    /** @var callable(int): string */
    private $randomBytes;

    /**
     * @param ?callable(int): string $randomBytes defaults to random_bytes
     */
    public function __construct(?callable $randomBytes = null)
    {
        $this->randomBytes = $randomBytes ?? static fn(int $length): string => random_bytes(max(1, $length));
    }

    public function next(): string
    {
        return Uuid::v4($this->randomBytes);
    }
}
