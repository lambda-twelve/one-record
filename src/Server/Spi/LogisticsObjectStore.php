<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Where logistics objects and their revisions live. Narrow on purpose so a
 * relational implementation (one row per revision, the graph as JSON-LD) is
 * straightforward, and transactional where it matters: saving revision N
 * must fail if the current revision is not N-1.
 */
interface LogisticsObjectStore
{
    public function latest(Iri $iri): ?StoredObject;

    public function revision(Iri $iri, int $revision): ?StoredObject;

    /**
     * The revision that was current at $at (the spec's ?at= query), or null
     * if the object did not exist yet.
     */
    public function at(Iri $iri, DateTimeImmutable $at): ?StoredObject;

    public function exists(Iri $iri): bool;

    /**
     * Revision 1.
     *
     * @throws StoreException ALREADY_EXISTS
     */
    public function create(LogisticsObject $object, DateTimeImmutable $at): StoredObject;

    /**
     * Revision $expectedCurrent + 1, only if $expectedCurrent is still current.
     *
     * @throws StoreException REVISION_CONFLICT, NOT_FOUND
     */
    public function saveRevision(LogisticsObject $object, int $expectedCurrent, DateTimeImmutable $at): StoredObject;

    /**
     * Removes every revision: the host's data-protection erasure. The API has
     * no delete; this is for the host, after it has closed access.
     */
    public function erase(Iri $iri): void;
}
