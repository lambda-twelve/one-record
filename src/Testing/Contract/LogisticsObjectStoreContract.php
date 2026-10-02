<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsObjectStore;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\TestCase;

/**
 * What every LogisticsObjectStore must do. Extend it, return your store from
 * createStore(), and the same tests that pass for the in-memory store prove
 * yours. Stores are expected to start empty.
 */
abstract class LogisticsObjectStoreContract extends TestCase
{
    abstract protected function createStore(): LogisticsObjectStore;

    protected function object(string $id, string $description = 'Books'): LogisticsObject
    {
        return ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, $description)->set(Cargo::coload, false)->build(new Iri('https://1r.example.com/logistics-objects/' . $id));
    }

    public function testCreateStoresRevisionOne(): void
    {
        $store = $this->createStore();
        $at = new DateTimeImmutable('2026-10-02T12:00:00.000Z');
        $object = $this->object('p1');

        $stored = $store->create($object, $at);

        self::assertSame(1, $stored->revision);
        self::assertSame(1, $stored->latestRevision);
        self::assertTrue($stored->isLatest());
        self::assertSame($at->format(DATE_RFC3339_EXTENDED), $stored->createdAt->format(DATE_RFC3339_EXTENDED));
        self::assertSame($at->format(DATE_RFC3339_EXTENDED), $stored->lastModified->format(DATE_RFC3339_EXTENDED));
        self::assertTrue($store->exists($object->iri));
        self::assertTrue($store->latest($object->iri)?->object->isSameAs($object), 'the object reads back identical');
        self::assertFalse($store->exists(new Iri('https://1r.example.com/logistics-objects/nope')));
        self::assertNull($store->latest(new Iri('https://1r.example.com/logistics-objects/nope')));
    }

    public function testCreatingTheSameUriTwiceIsAnAlreadyExistsError(): void
    {
        $store = $this->createStore();
        $store->create($this->object('p1'), new DateTimeImmutable('2026-10-02T12:00:00Z'));

        try {
            $store->create($this->object('p1', 'Again'), new DateTimeImmutable('2026-10-02T12:01:00Z'));
            self::fail('expected a StoreException');
        } catch (StoreException $e) {
            self::assertSame(StoreException::ALREADY_EXISTS, $e->kind);
        }
        self::assertSame('Books', $store->latest($this->object('p1')->iri)?->object->literal(Cargo::goodsDescription), 'the first object is untouched');
    }

    public function testSaveRevisionIsCompareAndSet(): void
    {
        $store = $this->createStore();
        $iri = $this->object('p1')->iri;
        $t1 = new DateTimeImmutable('2026-10-02T12:00:00.000Z');
        $t2 = new DateTimeImmutable('2026-10-02T13:00:00.000Z');
        $store->create($this->object('p1'), $t1);

        try {
            $store->saveRevision($this->object('p1', 'Stale'), 7, $t2);
            self::fail('a wrong expected revision must be refused');
        } catch (StoreException $e) {
            self::assertSame(StoreException::REVISION_CONFLICT, $e->kind);
        }
        try {
            $store->saveRevision($this->object('ghost', 'Nowhere'), 0, $t2);
            self::fail('an unknown object cannot get a revision');
        } catch (StoreException $e) {
            self::assertSame(StoreException::NOT_FOUND, $e->kind);
        }

        $second = $store->saveRevision($this->object('p1', 'Magazines'), 1, $t2);
        self::assertSame(2, $second->revision);
        self::assertSame(2, $second->latestRevision);
        self::assertSame($t1->format(DATE_RFC3339_EXTENDED), $second->createdAt->format(DATE_RFC3339_EXTENDED), 'createdAt is the object\'s, not the revision\'s');
        self::assertSame($t2->format(DATE_RFC3339_EXTENDED), $second->lastModified->format(DATE_RFC3339_EXTENDED));

        $first = $store->revision($iri, 1);
        self::assertNotNull($first);
        self::assertSame(1, $first->revision);
        self::assertSame(2, $first->latestRevision, 'an old revision knows the latest one');
        self::assertFalse($first->isLatest());
        self::assertSame('Books', $first->object->literal(Cargo::goodsDescription));
        self::assertSame('Magazines', $store->latest($iri)?->object->literal(Cargo::goodsDescription));
        self::assertNull($store->revision($iri, 3));
        self::assertNull($store->revision($iri, 0));
    }

    public function testAtAnswersTheRevisionCurrentAtAnInstant(): void
    {
        $store = $this->createStore();
        $iri = $this->object('p1')->iri;
        $store->create($this->object('p1'), new DateTimeImmutable('2026-10-02T12:00:00Z'));
        $store->saveRevision($this->object('p1', 'Magazines'), 1, new DateTimeImmutable('2026-10-02T13:00:00Z'));

        self::assertNull($store->at($iri, new DateTimeImmutable('2026-10-02T11:59:59Z')), 'before it existed');
        self::assertSame(1, $store->at($iri, new DateTimeImmutable('2026-10-02T12:00:00Z'))?->revision, 'the instant of creation counts');
        self::assertSame(1, $store->at($iri, new DateTimeImmutable('2026-10-02T12:59:59Z'))?->revision);
        self::assertSame(2, $store->at($iri, new DateTimeImmutable('2026-10-02T13:00:00Z'))?->revision);
        self::assertSame(2, $store->at($iri, new DateTimeImmutable('2030-01-01T00:00:00Z'))?->revision);
    }

    public function testStoredRevisionsAreSnapshots(): void
    {
        $store = $this->createStore();
        $object = $this->object('p1');
        $store->create($object, new DateTimeImmutable('2026-10-02T12:00:00Z'));

        // Editing the object handed in, or the one read back, must not rewrite history (AR-015).
        $object->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($object->iri, new Iri(Cargo::goodsDescription), \LambdaTwelve\OneRecord\Rdf\Literal::string('INJECTED')));
        $read = $store->latest($object->iri);
        self::assertNotNull($read);
        self::assertSame(['Books'], array_map(static fn($t): string => $t instanceof \LambdaTwelve\OneRecord\Rdf\Literal ? $t->lexical : '', $read->object->values(Cargo::goodsDescription)));
        $read->object->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($object->iri, new Iri(Cargo::goodsDescription), \LambdaTwelve\OneRecord\Rdf\Literal::string('INJECTED')));
        self::assertCount(1, $store->revision($object->iri, 1)?->object->values(Cargo::goodsDescription) ?? []);
    }

    public function testEraseRemovesEveryRevision(): void
    {
        $store = $this->createStore();
        $iri = $this->object('p1')->iri;
        $store->create($this->object('p1'), new DateTimeImmutable('2026-10-02T12:00:00Z'));
        $store->saveRevision($this->object('p1', 'Magazines'), 1, new DateTimeImmutable('2026-10-02T13:00:00Z'));

        $store->erase($iri);

        self::assertFalse($store->exists($iri));
        self::assertNull($store->latest($iri));
        self::assertNull($store->revision($iri, 1));
        self::assertNull($store->at($iri, new DateTimeImmutable('2030-01-01T00:00:00Z')));
        $store->erase($iri);
        self::assertSame(1, $store->create($this->object('p1'), new DateTimeImmutable('2026-10-02T14:00:00Z'))->revision, 'the URI can be used again');
    }
}
