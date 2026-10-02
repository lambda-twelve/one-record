<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsObjectStore;
use LambdaTwelve\OneRecord\Server\Spi\StoredObject;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;

/**
 * Every revision of every object in arrays. The reference implementation for
 * tests and bin/serve, and the behavioural contract a database-backed store
 * must match (see LogisticsObjectStoreContractTest).
 */
final class InMemoryLogisticsObjectStore implements LogisticsObjectStore
{
    /** @var array<string, list<array{object: LogisticsObject, at: DateTimeImmutable}>> IRI => revisions, index 0 = revision 1 */
    private array $revisions = [];

    public function latest(Iri $iri): ?StoredObject
    {
        $revisions = $this->revisions[$iri->value] ?? [];

        return $revisions === [] ? null : $this->stored($iri, \count($revisions));
    }

    public function revision(Iri $iri, int $revision): ?StoredObject
    {
        $revisions = $this->revisions[$iri->value] ?? [];

        return $revision >= 1 && $revision <= \count($revisions) ? $this->stored($iri, $revision) : null;
    }

    public function at(Iri $iri, DateTimeImmutable $at): ?StoredObject
    {
        $revisions = $this->revisions[$iri->value] ?? [];
        $found = null;
        foreach ($revisions as $index => $entry) {
            if ($entry['at'] <= $at) {
                $found = $index + 1;
            }
        }

        return $found === null ? null : $this->stored($iri, $found);
    }

    public function exists(Iri $iri): bool
    {
        return isset($this->revisions[$iri->value]);
    }

    public function create(LogisticsObject $object, DateTimeImmutable $at): StoredObject
    {
        if (isset($this->revisions[$object->iri->value])) {
            throw StoreException::alreadyExists($object->iri);
        }
        $this->revisions[$object->iri->value] = [['object' => self::snapshot($object), 'at' => $at]];

        return $this->stored($object->iri, 1);
    }

    public function saveRevision(LogisticsObject $object, int $expectedCurrent, DateTimeImmutable $at): StoredObject
    {
        $revisions = $this->revisions[$object->iri->value] ?? null;
        if ($revisions === null) {
            throw StoreException::notFound($object->iri);
        }
        if (\count($revisions) !== $expectedCurrent) {
            throw StoreException::revisionConflict($object->iri, $expectedCurrent, \count($revisions));
        }
        $this->revisions[$object->iri->value][] = ['object' => self::snapshot($object), 'at' => $at];

        return $this->stored($object->iri, $expectedCurrent + 1);
    }

    /**
     * Graph is mutable by design (builders and the applier edit one); a stored
     * revision must not change when a caller keeps editing the object it passed
     * in or the one it read back (AR-015). A database store gets this for free
     * by serialising; the reference store copies.
     */
    private static function snapshot(LogisticsObject $object): LogisticsObject
    {
        return $object->withGraph(new Graph($object->graph));
    }

    public function erase(Iri $iri): void
    {
        unset($this->revisions[$iri->value]);
    }

    private function stored(Iri $iri, int $revision): StoredObject
    {
        $revisions = $this->revisions[$iri->value];
        $entry = $revisions[$revision - 1];

        return new StoredObject(self::snapshot($entry['object']), $revision, \count($revisions), $revisions[0]['at'], $entry['at']);
    }
}
