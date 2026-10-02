<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use DateTimeImmutable;
use DateTimeZone;
use Psr\Clock\ClockInterface;

/**
 * The real clock, in UTC. The package never reads the time directly; hosts
 * without a PSR-20 implementation of their own can pass this one.
 */
final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
