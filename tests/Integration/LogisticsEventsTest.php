<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Event\LogisticsEventReceived;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;
use Psr\Http\Message\ResponseInterface;

#[CoversNothing]
final class LogisticsEventsTest extends ServerTestCase
{
    private function event(string $code, string $date, string $name = 'Departed', ?string $for = null): string
    {
        $event = [
            '@context' => ['cargo' => Cargo::NAMESPACE],
            '@type' => 'cargo:LogisticsEvent',
            'cargo:eventCode' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/StatusCode#' . $code],
            'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => $date],
            'cargo:eventName' => $name,
            'cargo:eventTimeType' => ['@id' => 'cargo:ACTUAL'],
        ];
        if ($for !== null) {
            $event['cargo:eventFor'] = ['@id' => $for, '@type' => 'cargo:Piece'];
        }

        return json_encode($event, JSON_THROW_ON_ERROR);
    }

    private function post(string $body, string $agent = self::PARTNER, string $id = 'piece-1'): ResponseInterface
    {
        return $this->request('POST', '/logistics-objects/' . $id . '/logistics-events', $agent, body: $body);
    }

    public function testPostingAnEventCreatesItAndRaisesAnEvent(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent, Permission::GetLogisticsEvent]);

        $response = $this->post($this->event('DEP', '2026-10-02T11:00:00Z'));

        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame('', (string) $response->getBody());
        self::assertSame(Cargo::LogisticsEvent, $response->getHeaderLine('Type'));
        $location = $response->getHeaderLine('Location');
        self::assertStringStartsWith($piece->iri->value . '/logistics-events/', $location);

        $received = $this->dispatcher->of(LogisticsEventReceived::class);
        self::assertCount(1, $received);
        self::assertSame(self::PARTNER, $received[0]->postedBy->iri->value);
        self::assertSame('https://onerecord.iata.org/ns/code-lists/StatusCode#DEP', $received[0]->event->eventCode());
        self::assertTrue($received[0]->event->matchesCode('DEP'));

        $one = $this->request('GET', substr($location, \strlen(self::BASE)));
        self::assertSame(200, $one->getStatusCode());
        self::assertSame(Cargo::LogisticsEvent, $one->getHeaderLine('Type'));
        self::assertSame('Fri, 02 Oct 2026 12:00:00 GMT', $one->getHeaderLine('Last-Modified'));
        $body = self::json($one);
        self::assertSame($location, $body['@id']);
        self::assertSame('Departed', $body['cargo:eventName']);
        self::assertSame(['@id' => 'cargo:ACTUAL'], $body['cargo:eventTimeType']);
    }

    public function testListingFiltersSortsAndPaginates(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent, Permission::GetLogisticsEvent]);

        self::assertSame(200, $this->request('GET', '/logistics-objects/piece-1/logistics-events')->getStatusCode());
        $empty = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events'));
        self::assertSame('api:Collection', $empty['@type']);
        self::assertSame(0, $empty['api:hasTotalItems']);
        self::assertArrayNotHasKey('api:hasItem', $empty);
        self::assertSame($piece->iri->value . '/logistics-events', $empty['@id']);

        $this->post($this->event('FOH', '2026-10-01T08:00:00Z', 'Freight on hand'));
        $this->clock->advance('+10 minutes');
        $this->post($this->event('RCS', '2026-10-01T09:00:00Z', 'Received from shipper'));
        $this->clock->advance('+10 minutes');
        $this->post($this->event('DEP', '2026-10-02T11:00:00Z', 'Departed'));

        $all = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events'));
        self::assertSame(3, $all['api:hasTotalItems']);
        $items = self::arr($all['api:hasItem']);
        self::assertSame(['Freight on hand', 'Received from shipper', 'Departed'], array_map(static fn($i): mixed => self::arr($i)['cargo:eventName'], $items), 'creation order by default');

        $one = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events?event-code=DEP'));
        self::assertSame(1, $one['api:hasTotalItems']);
        self::assertSame('Departed', self::arr($one['api:hasItem'])['cargo:eventName'], 'a single item is an object, not a list');

        $two = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events?event-code=FOH,DEP'));
        self::assertSame(2, $two['api:hasTotalItems']);

        $desc = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events?sort=DESC-eventDate&limit=2'));
        self::assertSame(['Departed', 'Received from shipper'], array_map(static fn($i): mixed => self::arr($i)['cargo:eventName'], self::arr($desc['api:hasItem'])));
        $page = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events?sort=ASC-eventDate&limit=1&skip=1'));
        self::assertSame('Received from shipper', self::arr($page['api:hasItem'])['cargo:eventName']);

        $after = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events?occurred-after=20261001T083000Z'));
        self::assertSame(2, $after['api:hasTotalItems']);
        $created = self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events?created-after=20261002T120500Z&created-before=20261002T121500Z'));
        self::assertSame(1, $created['api:hasTotalItems']);

        self::assertError($this->request('GET', '/logistics-objects/piece-1/logistics-events?sort=random'), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', '/logistics-objects/piece-1/logistics-events?limit=0'), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', '/logistics-objects/piece-1/logistics-events?created-after=notatime'), 400, 'Invalid query parameter');

        $head = $this->request('HEAD', '/logistics-objects/piece-1/logistics-events');
        self::assertSame(200, $head->getStatusCode());
        self::assertSame('', (string) $head->getBody());
        self::assertSame('Fri, 02 Oct 2026 12:20:00 GMT', $head->getHeaderLine('Last-Modified'), 'the list changed when the last event was added');
    }

    public function testEventValidationAndAccess(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent]);

        self::assertError($this->post($this->event('DEP', '2026-10-02T11:00:00Z'), self::STRANGER), 403);
        self::assertError($this->post($this->event('DEP', '2026-10-02T11:00:00Z'), id: 'nope'), 404);
        self::assertError($this->post('{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:Piece"}'), 400, 'Invalid resource');
        self::assertError($this->post('{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:LogisticsEvent", "cargo:eventName": "no date"}'), 400, 'Invalid resource');
        self::assertError($this->post($this->event('DEP', '2026-10-02T11:00:00Z', for: self::BASE . '/logistics-objects/other')), 400, 'Invalid resource');
        self::assertError($this->post('{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:LogisticsEvent", "cargo:eventDate": "2026-10-02T11:00:00Z", "cargo:coload": true}'), 400, 'Invalid resource');
        self::assertError($this->post('not json'), 400, 'Invalid body request');
        self::assertError($this->request('POST', '/logistics-objects/piece-1/logistics-events', self::PARTNER, ['Content-Type' => 'text/turtle'], 'x'), 415, 'Unsupported content type');
        self::assertSame(201, $this->post($this->event('DEP', '2026-10-02T11:00:00Z', for: $piece->iri->value))->getStatusCode(), 'eventFor naming this object is fine');

        // Reading events needs GET_LOGISTICS_EVENT, which this partner does not have.
        self::assertError($this->request('GET', '/logistics-objects/piece-1/logistics-events'), 403);
        self::assertError($this->request('GET', '/logistics-objects/piece-1/logistics-events/whatever'), 403);
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::GetLogisticsEvent]);
        self::assertError($this->request('GET', '/logistics-objects/piece-1/logistics-events/whatever'), 404, 'Resource not found');
    }

    public function testOversizedBodiesAreRefused(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent]);
        $huge = str_repeat('x', 1_100_000);

        self::assertError($this->post($this->event('DEP', '2026-10-02T11:00:00Z', $huge)), 413, 'Payload too large');
    }
}
