<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Event;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\StoredObject;

/**
 * A logistics object came into existence on this server, through the PHP API
 * or through POST /logistics-objects.
 */
final readonly class LogisticsObjectCreated
{
    public function __construct(
        public StoredObject $stored,
        public ?Iri $createdBy,
    ) {}
}
