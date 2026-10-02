<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Server\Event\LogisticsObjectCreated;
use LambdaTwelve\OneRecord\Server\Event\NotificationReceived;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class CreateObjectAndNotificationsTest extends ServerTestCase
{
    private const string FIXTURES = __DIR__ . '/../Fixtures/spec/2026-07/';

    public function testCreationIsInternalOnlyByDefault(): void
    {
        $body = (string) file_get_contents(self::FIXTURES . 'Piece.json');

        self::assertError($this->request('POST', '/logistics-objects', body: $body), 403, 'Not authorized');
        self::assertCount(0, $this->dispatcher->of(LogisticsObjectCreated::class));
    }

    public function testTheHolderCreatesObjectsOverHttp(): void
    {
        $body = (string) file_get_contents(self::FIXTURES . 'Piece.json');
        $response = $this->request('POST', '/logistics-objects', self::HOLDER, body: $body);

        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame('', (string) $response->getBody());
        self::assertSame(Cargo::Piece, $response->getHeaderLine('Type'));
        $location = $response->getHeaderLine('Location');
        self::assertMatchesRegularExpression('#^https://1r\.example\.com/logistics-objects/[0-9a-f-]{36}$#', $location);
        self::assertCount(1, $this->dispatcher->of(LogisticsObjectCreated::class));
        self::assertSame(self::HOLDER, $this->dispatcher->of(LogisticsObjectCreated::class)[0]->createdBy?->value);

        $read = $this->request('GET', substr($location, \strlen(self::BASE)), self::HOLDER);
        self::assertSame(200, $read->getStatusCode());
        self::assertSame('1', $read->getHeaderLine('Revision'));
        self::assertFalse(self::json($read)['cargo:coload']);

        // A predefined URI under this server is honoured; the same one again is a conflict.
        $withId = json_encode(['@context' => ['cargo' => Cargo::NAMESPACE], '@id' => self::BASE . '/logistics-objects/waybill-020-12345675', '@type' => 'cargo:Waybill', 'cargo:waybillNumber' => '12345675'], JSON_THROW_ON_ERROR);
        $created = $this->request('POST', '/logistics-objects', self::HOLDER, body: $withId);
        self::assertSame(201, $created->getStatusCode());
        self::assertSame(self::BASE . '/logistics-objects/waybill-020-12345675', $created->getHeaderLine('Location'));
        self::assertError($this->request('POST', '/logistics-objects', self::HOLDER, body: $withId), 409, 'Identifier conflict');

        $company = $this->request('POST', '/logistics-objects', self::HOLDER, body: (string) file_get_contents(self::FIXTURES . 'Company.json'));
        self::assertSame(201, $company->getStatusCode(), (string) $company->getBody());
        self::assertSame(Cargo::Company, $company->getHeaderLine('Type'), 'the most specific of the declared types');
    }

    public function testCreationValidatesTheBody(): void
    {
        $cases = [
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "cargo:coload": true}', 'Invalid resource'],
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:Value", "cargo:numericalValue": 1}', 'Invalid resource'],
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:Piece", "cargo:waybillType": "x"}', 'Invalid resource'],
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:Piece", "cargo:colour": "red"}', 'Invalid resource'],
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#", "api": "https://onerecord.iata.org/ns/api#"}, "@type": "cargo:Piece", "api:hasRevision": 7}', 'Invalid resource'],
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@id": "https://elsewhere.example/logistics-objects/x", "@type": "cargo:Piece"}', 'Invalid body request'],
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@graph": [], "@type": "cargo:Piece"}', 'Invalid body request'],
            ['{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:Piece", "cargo:x": {"@list": []}}', 'Invalid body request'],
            ['', 'Invalid body request'],
        ];
        foreach ($cases as [$body, $title]) {
            self::assertError($this->request('POST', '/logistics-objects', self::HOLDER, body: $body), 400, $title);
        }
    }

    public function testNotificationsAreAcceptedFromAnyAuthenticatedPartyAndRaised(): void
    {
        $body = (string) file_get_contents(self::FIXTURES . 'Notification_example1.json');
        $response = $this->request('POST', '/notifications', self::STRANGER, body: $body);

        self::assertSame(204, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame('', (string) $response->getBody());
        $received = $this->dispatcher->of(NotificationReceived::class);
        self::assertCount(1, $received);
        self::assertSame(NotificationEventType::LogisticsObjectCreated, $received[0]->notification->eventType);
        self::assertSame(self::STRANGER, $received[0]->sentBy->iri->value);

        self::assertError($this->request('POST', '/notifications', agent: null, body: $body), 401);
        self::assertError($this->request('POST', '/notifications', body: '{"@context": {"api": "https://onerecord.iata.org/ns/api#"}, "@type": "api:Notification"}'), 400, 'Invalid resource');
        self::assertError($this->request('POST', '/notifications', body: '{}'), 400);
        self::assertError($this->request('GET', '/notifications'), 405, 'Method not allowed');
    }
}
