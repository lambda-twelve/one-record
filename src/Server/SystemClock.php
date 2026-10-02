<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use DateTimeImmutable;
use DateTimeZone;
use Psr\Clock\ClockInterface;

/**
 * The real clock, in UTC, at millisecond precision. JSON-LD timestamps are
 * written with milliseconds (Literal::dateTime), so a finer clock would make
 * a stored document differ from the instant the server computed; truncating
 * here keeps every store, in memory or in a database, byte-identical.
 * Hosts with a PSR-20 clock of their own should truncate the same way.
 */
final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        return $now->setTime((int) $now->format('G'), (int) $now->format('i'), (int) $now->format('s'), (int) $now->format('v') * 1000);
    }
}
