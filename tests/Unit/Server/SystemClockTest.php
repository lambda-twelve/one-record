<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Server\SystemClock;
use PHPUnit\Framework\TestCase;

final class SystemClockTest extends TestCase
{
    public function testTicksInUtcMillisecondsSoTimestampsRoundTrip(): void
    {
        $now = (new SystemClock())->now();

        self::assertSame('UTC', $now->getTimezone()->getName());
        self::assertSame('000', substr($now->format('u'), 3), 'no microseconds');
        $written = new DateTimeImmutable(Literal::dateTime($now)->lexical);
        self::assertSame($now->format('Y-m-d\TH:i:s.v'), $written->format('Y-m-d\TH:i:s.v'), 'the written literal is the same instant');
    }
}
