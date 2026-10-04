<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final class FixedClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(string|DateTimeImmutable $now = '2026-10-02T12:00:00Z')
    {
        $this->now = $now instanceof DateTimeImmutable ? $now : new DateTimeImmutable($now);
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    public function advance(string $interval): void
    {
        $this->now = $this->now->modify($interval);
    }

    /**
     * Jumps to an absolute instant: expiry boundaries, "a year later".
     */
    public function set(string|DateTimeImmutable $now): void
    {
        $this->now = $now instanceof DateTimeImmutable ? $now : new DateTimeImmutable($now);
    }
}
