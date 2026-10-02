<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * api:RequestStatusEntry (2.3): a status an action request used to have, from
 * when, and who moved it on.
 */
final readonly class RequestStatusEntry
{
    public function __construct(
        public RequestStatus $status,
        public DateTimeImmutable $since,
        public ?Iri $changedBy = null,
    ) {}
}
