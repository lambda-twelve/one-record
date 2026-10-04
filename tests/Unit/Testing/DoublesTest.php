<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Testing;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Client\ClientException;
use LambdaTwelve\OneRecord\Client\DeliveryVerdict;
use LambdaTwelve\OneRecord\Client\OneRecordClient;
use LambdaTwelve\OneRecord\Client\OneRecordHttpException;
use LambdaTwelve\OneRecord\Client\StaticTokenProvider;
use LambdaTwelve\OneRecord\Client\TokenEndpointException;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Testing\FakeHttpClient;
use LambdaTwelve\OneRecord\Testing\FixedClock;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

#[CoversClass(FixedClock::class)]
#[CoversClass(DeliveryVerdict::class)]
#[CoversClass(TokenEndpointException::class)]
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

        // A request PSR-18 calls unusable cannot be repaired by sending it again (R7-008).
        $request = new class ('missing Host header') extends RuntimeException implements \Psr\Http\Client\RequestExceptionInterface {
            public function getRequest(): RequestInterface
            {
                throw new RuntimeException('not needed');
            }
        };
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of($request));
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of(new ClientException('POST failed: missing Host header', 0, $request)));

        // The SDK client wraps its causes; the verdict looks through the wrapper (drupal3.md, item 1).
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of(new ClientException('POST https://partner.example/notifications failed: connection refused', 0, $network)));
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of(new ClientException('wrapped twice', 0, new RuntimeException('once', 0, $http(503)))));
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of(new TokenEndpointException(503, null, 'https://idp.example/token')), 'the partner\'s token endpoint is down: not now');
        self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of(new TokenEndpointException(429, null, 'https://idp.example/token')));
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of(new TokenEndpointException(400, 'invalid_client', 'https://idp.example/token')), 'refused credentials are final');
        self::assertSame(DeliveryVerdict::Reject, DeliveryVerdict::of(new TokenEndpointException(401, null, 'https://idp.example/token')));
    }

    public function testANotificationThatFailsInTransportThroughTheClientIsRetried(): void
    {
        $factory = new Psr17Factory();
        $network = new class ('connection refused') extends RuntimeException implements NetworkExceptionInterface {
            public function getRequest(): RequestInterface
            {
                throw new RuntimeException('not needed');
            }
        };
        $information = \LambdaTwelve\OneRecord\Api\ServerInformation::for(new Iri('https://partner.example'), new Iri('https://partner.example/logistics-objects/them'), [\LambdaTwelve\OneRecord\Spec\ApiVersion::V2_3_0], [\LambdaTwelve\OneRecord\Spec\DataModelVersion::V3_3]);
        $http = (new FakeHttpClient())
            ->queue(new Response(200, ['Content-Type' => 'application/ld+json; version=2.3.0'], json_encode($information->toJsonLd(), JSON_THROW_ON_ERROR)))
            ->queue($network);
        $client = new OneRecordClient($http, $factory, $factory, new StaticTokenProvider('tok'), 'https://partner.example/', null, new FixedClock());

        try {
            $client->sendNotification(new Notification(NotificationEventType::LogisticsObjectCreated, new Iri('https://1r.example.com/logistics-objects/p1'), Cargo::Piece), 'n-1');
            self::fail('transport failed');
        } catch (ClientException $e) {
            self::assertSame(DeliveryVerdict::Retry, DeliveryVerdict::of($e), 'what the worker catches is what it classifies');
        }
    }
}
