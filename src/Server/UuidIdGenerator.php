<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Model\Uuid;
use LambdaTwelve\OneRecord\Server\Spi\IdGenerator;

/**
 * Random UUIDs (version 4) from an injectable byte source, so tests can be
 * deterministic.
 */
final class UuidIdGenerator implements IdGenerator
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
