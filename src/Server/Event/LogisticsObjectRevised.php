<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Event;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\StoredObject;

/**
 * An accepted change produced a new revision.
 *
 * @param list<string> $changedProperties property IRIs changed on the object itself
 */
final readonly class LogisticsObjectRevised
{
    /**
     * @param list<string> $changedProperties
     */
    public function __construct(
        public StoredObject $stored,
        public Iri $changeRequest,
        public array $changedProperties,
    ) {}
}
