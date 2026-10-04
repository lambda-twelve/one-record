<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Testing;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Client\ClientException;
use LambdaTwelve\OneRecord\Client\DeliveryVerdict;
use LambdaTwelve\OneRecord\Client\OneRecordHttpException;
use LambdaTwelve\OneRecord\Testing\FixedClock;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

#[CoversClass(FixedClock::class)]
#[CoversClass(DeliveryVerdict::class)]
final class DoublesTest extends TestCase
{
    public function testTheFixedClockAdvancesAndJumps(): void
    {
        $clock = new FixedClock('2026-10-02T12:00:00Z');
        $clock->advance('+1 hour');
        self::assertSame('2026-10-02T13:00:00+00:00', $clock->now()->format(DATE_ATOM));
        $clock->set('2027-10-02T12:00:00Z');
        self::assertSame('2027-10-02T12:00:00+00:00', $clock->now()->format(DATE_ATOM));
        $clock->set(new DateTimeImmutable('2026-01-01T00:00:00Z'));
        self::assertSame('2026-01-01T00:00:00+00:00', $clock->now()->format(DATE_ATOM));
    }

    public function testDeliveryVerdictsFollowTheOutboxPolicy(): void
    {
        $http = static fn(int $status): OneRecordHttpException => new OneRecordHttpException($status, null, new Response($status), 'POST', 'https://partner.example/notifications');
        $network = new class ('connection refused') extends RuntimeException implements NetworkExceptionInterface {
            public function getRequest(): RequestInterface
            {
                throw new RuntimeException('not needed');
            }
        };

        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of($http(500)));
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of($http(503)));
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of($http(408)));
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of($http(429)));
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of($network), 'the network, not the recipient, failed');
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of($http(400)));
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of($http(401)));
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of($http(404)));
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of(new ClientException('the client refuses to send a token to a foreign origin')), 'a sending-side defect is not transient');
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of(new RuntimeException('anything else')));
    }
}
