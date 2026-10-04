<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\EventQuery;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsEventStore;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;

/**
 * The tests behind LogisticsEventStoreContract, as a trait, for hosts whose test cases must
 * extend a framework base class (Laravel's Testbench, Drupal's KernelTestBase)
 * and so cannot extend the abstract contract. Use it in any PHPUnit TestCase
 * and implement the abstract hook(s); the abstract class is this trait on a
 * bare TestCase.
 */
trait LogisticsEventStoreContractTests
{
    protected const string OBJECT = 'https://1r.example.com/logistics-objects/p1';
    protected const string OTHER = 'https://1r.example.com/logistics-objects/p2';

    abstract protected function createStore(): LogisticsEventStore;

    protected function event(string $id, string $code, string $occurred, string $created, string $object = self::OBJECT): LogisticsEvent
    {
        return ObjectBuilder::ofEvent()
            ->set(Cargo::eventDate, Values::dateTime(new DateTimeImmutable($occurred)))
            ->set(Cargo::eventCode, Values::code('StatusCode', $code))
            ->set(Cargo::eventName, 'Event ' . $id)
            ->buildEvent(new Iri($object . '/logistics-events/' . $id), new Iri($object), new DateTimeImmutable($created));
    }

    /**
     * @param list<LogisticsEvent> $events
     * @return list<string>
     */
    private static function ids(array $events): array
    {
        return array_map(static fn(LogisticsEvent $e): string => basename($e->iri->value), $events);
    }

    public function testAppendGetAndListInCreationOrder(): void
    {
        $store = $this->createStore();
        $a = $this->event('a', 'DEP', '2026-10-02T10:00:00Z', '2026-10-02T12:00:00Z');
        $b = $this->event('b', 'ARR', '2026-10-02T09:00:00Z', '2026-10-02T12:05:00Z');
        $store->append($a);
        $store->append($b);
        $store->append($this->event('c', 'DEP', '2026-10-02T10:00:00Z', '2026-10-02T12:10:00Z', self::OTHER));

        $got = $store->get($a->iri);
        self::assertNotNull($got);
        self::assertSame($a->iri->value, $got->iri->value);
        self::assertSame(self::OBJECT, $got->logisticsObject->value);
        self::assertSame('2026-10-02T12:00:00+00:00', $got->created->format(DATE_ATOM));
        self::assertTrue($got->matchesCode('DEP'));
        self::assertNull($store->get(new Iri(self::OBJECT . '/logistics-events/nope')));

        self::assertSame(['a', 'b'], self::ids($store->query(new Iri(self::OBJECT), EventQuery::all())), 'only this object\'s events, oldest created first');
        self::assertSame(['c'], self::ids($store->query(new Iri(self::OTHER), EventQuery::all())));
        self::assertSame([], $store->query(new Iri('https://1r.example.com/logistics-objects/none'), EventQuery::all()));
    }

    public function testFiltersSortingAndPaging(): void
    {
        $store = $this->createStore();
        $store->append($this->event('a', 'DEP', '2026-10-02T10:00:00Z', '2026-10-02T12:00:00Z'));
        $store->append($this->event('b', 'ARR', '2026-10-02T09:00:00Z', '2026-10-02T12:05:00Z'));
        $store->append($this->event('c', 'DEP', '2026-10-02T11:00:00Z', '2026-10-02T12:10:00Z'));
        $object = new Iri(self::OBJECT);

        self::assertSame(['a', 'c'], self::ids($store->query($object, new EventQuery(eventCodes: ['DEP']))));
        self::assertSame(['a', 'c'], self::ids($store->query($object, new EventQuery(eventCodes: ['https://onerecord.iata.org/ns/code-lists/StatusCode#DEP']))), 'codes may be given as IRIs');
        self::assertSame(['a', 'b', 'c'], self::ids($store->query($object, new EventQuery(eventCodes: ['DEP', 'ARR']))));
        self::assertSame([], self::ids($store->query($object, new EventQuery(eventCodes: ['RCS']))));
        self::assertSame(['b', 'c'], self::ids($store->query($object, new EventQuery(createdAfter: new DateTimeImmutable('2026-10-02T12:00:00Z')))), 'after is exclusive');
        self::assertSame(['a', 'b'], self::ids($store->query($object, new EventQuery(createdBefore: new DateTimeImmutable('2026-10-02T12:10:00Z')))), 'before is exclusive');
        self::assertSame(['a', 'c'], self::ids($store->query($object, new EventQuery(occurredAfter: new DateTimeImmutable('2026-10-02T09:30:00Z')))));
        self::assertSame(['b', 'a'], self::ids($store->query($object, new EventQuery(occurredBefore: new DateTimeImmutable('2026-10-02T10:30:00Z'), sort: EventQuery::SORT_EVENT_ASC))));
        self::assertSame(['c', 'a', 'b'], self::ids($store->query($object, new EventQuery(sort: EventQuery::SORT_EVENT_DESC))));
        self::assertSame(['c', 'b', 'a'], self::ids($store->query($object, new EventQuery(sort: EventQuery::SORT_CREATED_DESC))));
        self::assertSame(['b', 'c'], self::ids($store->query($object, new EventQuery(limit: 2, skip: 1))));
        self::assertSame([], self::ids($store->query($object, new EventQuery(skip: 3))));
    }

    public function testLastModifiedIsTheNewestEventAndARepeatedIriIsRefused(): void
    {
        $store = $this->createStore();
        $object = new Iri(self::OBJECT);
        self::assertNull($store->lastModified($object));

        $store->append($this->event('a', 'DEP', '2026-10-02T10:00:00Z', '2026-10-02T12:05:00Z'));
        $store->append($this->event('b', 'ARR', '2026-10-02T09:00:00Z', '2026-10-02T12:00:00Z'));
        self::assertSame('2026-10-02T12:05:00+00:00', $store->lastModified($object)?->format(DATE_ATOM), 'the newest event, not the last appended');

        try {
            $store->append($this->event('a', 'RCS', '2026-10-02T10:00:00Z', '2026-10-02T12:30:00Z'));
            self::fail('an event IRI is appended once');
        } catch (StoreException $e) {
            self::assertSame(StoreException::ALREADY_EXISTS, $e->kind);
        }
        self::assertTrue($store->get(new Iri(self::OBJECT . '/logistics-events/a'))?->matchesCode('DEP'), 'the original stays');

        // The same IRI filed under another object is still the same IRI (AR-022).
        $sameIriOtherObject = ObjectBuilder::ofEvent()
            ->set(Cargo::eventDate, Values::dateTime(new DateTimeImmutable('2026-10-02T10:00:00Z')))
            ->buildEvent(new Iri(self::OBJECT . '/logistics-events/a'), new Iri(self::OTHER), new DateTimeImmutable('2026-10-02T12:30:00Z'));
        try {
            $store->append($sameIriOtherObject);
            self::fail('event IRIs are unique on the server');
        } catch (StoreException $e) {
            self::assertSame(StoreException::ALREADY_EXISTS, $e->kind);
        }
        self::assertSame([], $store->query(new Iri(self::OTHER), EventQuery::all()));
    }

    public function testEraseForRemovesOneObjectsEventsOnly(): void
    {
        $store = $this->createStore();
        $store->append($this->event('a', 'DEP', '2026-10-02T10:00:00Z', '2026-10-02T12:00:00Z'));
        $store->append($this->event('c', 'DEP', '2026-10-02T10:00:00Z', '2026-10-02T12:10:00Z', self::OTHER));

        $store->eraseFor(new Iri(self::OBJECT));

        self::assertSame([], $store->query(new Iri(self::OBJECT), EventQuery::all()));
        self::assertNull($store->get(new Iri(self::OBJECT . '/logistics-events/a')));
        self::assertNull($store->lastModified(new Iri(self::OBJECT)));
        self::assertSame(['c'], self::ids($store->query(new Iri(self::OTHER), EventQuery::all())));
    }

    public function testStoredEventsAreSnapshots(): void
    {
        $store = $this->createStore();
        $event = $this->event('a', 'DEP', '2026-10-02T10:00:00Z', '2026-10-02T12:00:00Z');
        $store->append($event);

        // Editing the event handed in, or the one read back, must not edit the log (R2-005).
        $event->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($event->iri, new Iri(Cargo::eventName), \LambdaTwelve\OneRecord\Rdf\Literal::string('after-write')));
        $read = $store->get($event->iri);
        self::assertNotNull($read);
        self::assertCount(1, $read->graph->objects($event->iri, Cargo::eventName));
        $read->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($event->iri, new Iri(Cargo::eventName), \LambdaTwelve\OneRecord\Rdf\Literal::string('after-read')));
        self::assertCount(1, $store->get($event->iri)?->graph->objects($event->iri, Cargo::eventName) ?? []);
        $listed = $store->query(new Iri(self::OBJECT), EventQuery::all());
        self::assertCount(1, $listed[0]->graph->objects($event->iri, Cargo::eventName));
    }
}
