<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Client;

use DateTimeImmutable;
use InvalidArgumentException;
use LambdaTwelve\OneRecord\Api\ServerInformation;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Client\ClientException;
use LambdaTwelve\OneRecord\Client\OneRecordClient;
use LambdaTwelve\OneRecord\Client\OneRecordHttpException;
use LambdaTwelve\OneRecord\Client\StaticTokenProvider;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Testing\ArrayCache;
use LambdaTwelve\OneRecord\Testing\FakeHttpClient;
use LambdaTwelve\OneRecord\Testing\FixedClock;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

/**
 * The client against a scripted partner: version negotiation, headers, error
 * mapping and the bulk fallback. The full request set runs against the real
 * server in tests/Integration/ClientAgainstServerTest.
 */
final class OneRecordClientTest extends TestCase
{
    private const string PARTNER = 'https://1r.partner.example';

    private FakeHttpClient $http;

    protected function setUp(): void
    {
        $this->http = new FakeHttpClient();
    }

    /**
     * @param list<ApiVersion> $theirs
     */
    private function serverInformation(array $theirs): Response
    {
        $information = ServerInformation::for(new Iri(self::PARTNER), new Iri(self::PARTNER . '/logistics-objects/them'), $theirs, [DataModelVersion::V3_2]);

        return $this->jsonLd(200, $information->toJsonLd(), $theirs[0]);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    private function jsonLd(int $status, array $body, ApiVersion $version = ApiVersion::V2_3_0, array $headers = []): Response
    {
        return new Response($status, ['Content-Type' => 'application/ld+json; version=' . $version->value, 'Content-Language' => 'en-US', ...$headers], json_encode($body, JSON_THROW_ON_ERROR));
    }

    private function client(?ArrayCache $cache = null): OneRecordClient
    {
        $factory = new Psr17Factory();

        return new OneRecordClient($this->http, $factory, $factory, new StaticTokenProvider('tok'), self::PARTNER . '/', $cache, new FixedClock());
    }

    public function testNegotiatesTheHighestCommonVersionAndSpeaksIt(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_2_0]));
        $this->http->queue($this->jsonLd(200, ['@context' => ['cargo' => 'https://onerecord.iata.org/ns/cargo#'], '@id' => self::PARTNER . '/logistics-objects/p1', '@type' => 'cargo:Piece'], ApiVersion::V2_2_0, ['Revision' => '3', 'Latest-Revision' => '4', 'Last-Modified' => 'Fri, 02 Oct 2026 12:00:00 GMT', 'Type' => 'https://onerecord.iata.org/ns/cargo#Piece']));
        $client = $this->client();

        self::assertSame(ApiVersion::V2_2_0, $client->apiVersion());
        $response = $client->getLogisticsObject(self::PARTNER . '/logistics-objects/p1', at: new DateTimeImmutable('2026-10-02T12:00:00+02:00'), embedded: true);

        self::assertSame('application/ld+json; version=2.3.0', $this->http->requests[0]->getHeaderLine('Accept'), 'discovery asks for the highest we speak');
        self::assertSame('Bearer tok', $this->http->requests[0]->getHeaderLine('Authorization'));
        self::assertSame(self::PARTNER . '/', (string) $this->http->requests[0]->getUri());
        $read = $this->http->requests[1];
        self::assertSame('application/ld+json; version=2.2.0', $read->getHeaderLine('Accept'), 'then the negotiated version');
        self::assertSame(self::PARTNER . '/logistics-objects/p1?at=20261002T100000Z&embedded=true', (string) $read->getUri(), 'the spec\'s basic timestamp form, in UTC');
        self::assertSame(3, $response->revision);
        self::assertSame(4, $response->latestRevision);
        self::assertFalse($response->isLatest());
        self::assertSame('2026-10-02T12:00:00+00:00', $response->lastModified?->format(DATE_ATOM));
        self::assertSame('https://onerecord.iata.org/ns/cargo#Piece', $response->type);
        self::assertSame(ApiVersion::V2_2_0, $response->apiVersion);
        self::assertSame(['https://onerecord.iata.org/ns/cargo#Piece'], $response->object?->types());
    }

    public function testNoCommonVersionIsAnError(): void
    {
        $this->http->queue($this->jsonLd(200, array_replace(ServerInformation::for(new Iri(self::PARTNER), new Iri(self::PARTNER . '/x'), [], [])->toJsonLd(), ['api:hasSupportedApiVersion' => '1.9.0'])));
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('supports API 1.9.0');
        $this->client()->apiVersion();
    }

    public function testServerInformationIsCachedAndAVersionCanBeForced(): void
    {
        $cache = new ArrayCache();
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0, ApiVersion::V2_2_0]));
        $client = $this->client($cache);
        self::assertSame(ApiVersion::V2_3_0, $client->apiVersion());

        $http = $this->http = new FakeHttpClient();
        $again = $this->client($cache);
        self::assertSame(ApiVersion::V2_3_0, $again->apiVersion(), 'answered from the cache without a request');
        self::assertSame([], $http->requests);

        $forced = $again->withApiVersion(ApiVersion::V2_2_0);
        $http->queue(new Response(204, ['Content-Type' => 'application/ld+json; version=2.2.0']));
        $forced->revokeActionRequest(self::PARTNER . '/action-requests/r1');
        $revoke = $http->lastRequest();
        self::assertSame('application/ld+json; version=2.2.0', $revoke->getHeaderLine('Accept'));
        self::assertSame('DELETE', $revoke->getMethod());
    }

    public function testHttpErrorsCarryTheParsedApiError(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0]));
        $this->http->queue($this->jsonLd(403, [
            '@context' => ['api' => 'https://onerecord.iata.org/ns/api#'],
            '@type' => 'api:Error',
            'api:hasTitle' => 'Not authorized to perform action',
            'api:hasErrorDetail' => ['@type' => 'api:ErrorDetail', 'api:hasCode' => '403', 'api:hasMessage' => 'No GET_LOGISTICS_OBJECT permission.'],
        ]));
        $client = $this->client();
        try {
            $client->getLogisticsObject(self::PARTNER . '/logistics-objects/secret');
            self::fail('expected an exception');
        } catch (OneRecordHttpException $e) {
            self::assertSame(403, $e->status);
            self::assertTrue($e->isForbidden());
            self::assertSame('Not authorized to perform action', $e->error?->title);
            self::assertSame('403', $e->error->details[0]->code);
            self::assertStringContainsString('answered 403: Not authorized to perform action (No GET_LOGISTICS_OBJECT permission.)', $e->getMessage());
        }

        // A non-ONE-Record error page still surfaces the status.
        $this->http->queue(new Response(502, ['Content-Type' => 'text/html'], '<html>bad gateway</html>'));
        try {
            $client->headLogisticsObject(self::PARTNER . '/logistics-objects/secret');
            self::fail('expected an exception');
        } catch (OneRecordHttpException $e) {
            self::assertSame(502, $e->status);
            self::assertNull($e->error);
        }

        // Unexpected success codes and unreadable bodies are client exceptions too.
        $this->http->queue(new Response(200, [], ''));
        $this->expectException(ClientException::class);
        $client->revokeActionRequest(self::PARTNER . '/action-requests/r1');
    }

    public function testBulkEventsFallBackToPerObjectPostsWhenTheEndpointIsMissing(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0]));
        $this->http->queue($this->jsonLd(404, ['@context' => ['api' => 'https://onerecord.iata.org/ns/api#'], '@type' => 'api:Error', 'api:hasTitle' => 'Resource not found']));
        $this->http->queue(new Response(201, ['Location' => self::PARTNER . '/logistics-objects/a/logistics-events/e1']));
        $this->http->queue($this->jsonLd(403, ['@context' => ['api' => 'https://onerecord.iata.org/ns/api#'], '@type' => 'api:Error', 'api:hasTitle' => 'Not authorized to perform action']));
        $event = ['@context' => ['cargo' => 'https://onerecord.iata.org/ns/cargo#'], '@type' => 'cargo:LogisticsEvent', 'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z']];

        $results = $this->client()->postLogisticsEvents($event, [self::PARTNER . '/logistics-objects/a', self::PARTNER . '/logistics-objects/b']);

        self::assertSame(self::PARTNER . '/logistics-events', (string) $this->http->requests[1]->getUri(), 'the bulk endpoint was tried first');
        $bulkBody = json_decode((string) $this->http->requests[1]->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($bulkBody);
        self::assertIsArray($bulkBody['cargo:eventFor']);
        self::assertCount(2, $bulkBody['cargo:eventFor']);
        self::assertSame(self::PARTNER . '/logistics-objects/a/logistics-events', (string) $this->http->requests[2]->getUri());
        self::assertCount(2, $results);
        self::assertTrue($results[0]->created());
        self::assertSame(self::PARTNER . '/logistics-objects/a/logistics-events/e1', $results[0]->event?->value);
        self::assertSame(403, $results[1]->status);
        self::assertSame('Not authorized to perform action', $results[1]->error?->title);
    }

    public function testAt22BulkIsNotEvenTried(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_2_0]));
        $this->http->queue(new Response(201, ['Location' => self::PARTNER . '/logistics-objects/a/logistics-events/e1']));
        $event = ['@context' => ['cargo' => 'https://onerecord.iata.org/ns/cargo#'], '@type' => 'cargo:LogisticsEvent', 'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z']];

        $results = $this->client()->postLogisticsEvents($event, [self::PARTNER . '/logistics-objects/a']);

        self::assertCount(2, $this->http->requests);
        self::assertSame(self::PARTNER . '/logistics-objects/a/logistics-events', (string) $this->http->requests[1]->getUri());
        self::assertTrue($results[0]->created());
    }

    public function testSubscriptionsAnswerAsOneOrAsACollection(): void
    {
        $subscription = ['@type' => 'api:Subscription', 'api:hasSubscriber' => ['@id' => self::PARTNER . '/logistics-objects/them'], 'api:hasTopicType' => ['@id' => 'api:LOGISTICS_OBJECT_TYPE'], 'api:hasTopic' => ['@type' => 'http://www.w3.org/2001/XMLSchema#anyURI', '@value' => 'https://onerecord.iata.org/ns/cargo#Piece'], 'api:includeSubscriptionEventType' => [['@id' => 'api:LOGISTICS_OBJECT_UPDATED']]];
        $context = ['@context' => ['api' => 'https://onerecord.iata.org/ns/api#', 'cargo' => 'https://onerecord.iata.org/ns/cargo#']];
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0]));
        $this->http->queue($this->jsonLd(200, $context + $subscription));
        $this->http->queue($this->jsonLd(200, $context + ['@id' => self::PARTNER . '/subscriptions', '@type' => 'api:Collection', 'api:hasTotalItems' => 2, 'api:hasItem' => [$subscription, $subscription]]));
        $this->http->queue($this->jsonLd(200, $context + ['@id' => self::PARTNER . '/subscriptions', '@type' => 'api:Collection', 'api:hasTotalItems' => 0]));
        $client = $this->client();

        self::assertCount(1, $client->getSubscriptions(TopicType::Type, 'https://onerecord.iata.org/ns/cargo#Piece'));
        self::assertCount(2, $client->getSubscriptions(TopicType::Type, 'https://onerecord.iata.org/ns/cargo#Piece'));
        self::assertSame([], $client->getSubscriptions(TopicType::Type, 'https://onerecord.iata.org/ns/cargo#Piece'));
        self::assertSame(self::PARTNER . '/subscriptions?topicType=LOGISTICS_OBJECT_TYPE&topic=https%3A%2F%2Fonerecord.iata.org%2Fns%2Fcargo%23Piece', (string) $this->http->requests[1]->getUri());
    }

    public function testTransportFailuresBecomeClientExceptions(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0]));
        $this->http->queue(new class extends RuntimeException implements \Psr\Http\Client\ClientExceptionInterface {
            public function __construct()
            {
                parent::__construct('connection refused');
            }
        });
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('connection refused');
        $this->client()->headLogisticsObject(self::PARTNER . '/logistics-objects/p1');
    }

    public function testTheEndpointMustBeAbsolute(): void
    {
        $factory = new Psr17Factory();
        $this->expectException(InvalidArgumentException::class);
        new OneRecordClient($this->http, $factory, $factory, new StaticTokenProvider('t'), 'partner.example');
    }

    public function testRequestsCanBeInspected(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0]));
        $this->http->queue(static fn(RequestInterface $r): Response => new Response(204, ['Content-Type' => 'application/ld+json; version=2.3.0']));
        $this->client()->sendNotification(new \LambdaTwelve\OneRecord\Api\Notification(\LambdaTwelve\OneRecord\Api\NotificationEventType::LogisticsObjectUpdated, new Iri(self::PARTNER . '/logistics-objects/p1')));
        $request = $this->http->requests[1];
        self::assertSame('POST', $request->getMethod());
        self::assertSame(self::PARTNER . '/notifications', (string) $request->getUri());
        self::assertSame('application/ld+json; version=2.3.0', $request->getHeaderLine('Content-Type'));
        self::assertStringContainsString('"api:LOGISTICS_OBJECT_UPDATED"', (string) $request->getBody());
    }

    public function testCredentialsGoOnlyToThePartnersOrigin(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0]));
        $client = $this->client();
        $client->apiVersion();
        $foreign = [
            'https://evil.example/logistics-objects/p',
            'http://1r.partner.example/logistics-objects/p',
            'https://1r.partner.example:8443/logistics-objects/p',
            'https://1r.partner.example.evil.example/logistics-objects/p',
            'https://user@1r.partner.example/logistics-objects/p',
            'https://1r.partner.example@evil.example/logistics-objects/p',
        ];
        foreach ($foreign as $url) {
            try {
                $client->getLogisticsObject($url);
                self::fail('expected a refusal for ' . $url);
            } catch (ClientException $e) {
                self::assertStringContainsString('sends its credentials only to that server', $e->getMessage());
            }
        }
        self::assertCount(1, $this->http->requests, 'nothing left the process for a foreign IRI');

        // Same origin spelled differently is fine; so is an origin the host declared.
        $this->http->queue(new Response(200, ['Content-Type' => 'application/ld+json; version=2.3.0', 'Revision' => '1', 'Latest-Revision' => '1']));
        $client->headLogisticsObject('HTTPS://1R.PARTNER.EXAMPLE:443/logistics-objects/p');
        $factory = new Psr17Factory();
        $this->http->queue($this->serverInformation([ApiVersion::V2_3_0]));
        $this->http->queue(new Response(200, ['Content-Type' => 'application/ld+json; version=2.3.0', 'Revision' => '1', 'Latest-Revision' => '1']));
        $wide = new OneRecordClient($this->http, $factory, $factory, new StaticTokenProvider('tok'), self::PARTNER, additionalOrigins: ['https://objects.partner.example']);
        $wide->headLogisticsObject('https://objects.partner.example/logistics-objects/p');
        self::assertSame('Bearer tok', $this->http->lastRequest()->getHeaderLine('Authorization'));

        $this->expectException(InvalidArgumentException::class);
        new OneRecordClient($this->http, $factory, $factory, new StaticTokenProvider('tok'), self::PARTNER, additionalOrigins: ['ftp://files.partner.example']);
    }

    public function testAr026AnExpiringDelegationIsNotSilentlyWidenedForA22Partner(): void
    {
        $this->http->queue($this->serverInformation([ApiVersion::V2_2_0]));
        $client = $this->client();
        $delegation = new \LambdaTwelve\OneRecord\Api\AccessDelegation([\LambdaTwelve\OneRecord\Api\Permission::GetLogisticsObject], [new Iri(self::PARTNER . '/logistics-objects/them')], [new Iri(self::PARTNER . '/logistics-objects/p1')], expiresAt: new DateTimeImmutable('2026-10-02T12:01:00Z'));

        try {
            $client->requestAccessDelegation($delegation);
            self::fail('a 2.2 partner cannot be asked for a time-limited delegation');
        } catch (ClientException $e) {
            self::assertStringContainsString('cannot express api:expiresAt', $e->getMessage());
        }
        self::assertCount(1, $this->http->requests, 'nothing was sent');

        $this->http->queue(new Response(201, ['Location' => self::PARTNER . '/action-requests/d1']));
        $unlimited = new \LambdaTwelve\OneRecord\Api\AccessDelegation([\LambdaTwelve\OneRecord\Api\Permission::GetLogisticsObject], [new Iri(self::PARTNER . '/logistics-objects/them')], [new Iri(self::PARTNER . '/logistics-objects/p1')]);
        self::assertSame(self::PARTNER . '/action-requests/d1', $client->requestAccessDelegation($unlimited)->value);
    }
}
