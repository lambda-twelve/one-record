<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * The outcome of posting one event to one object, whether it went through
 * the 2.3 bulk endpoint or a per-object POST.
 */
final readonly class BulkEventResult
{
    public function __construct(
        public Iri $logisticsObject,
        public int $status,
        public ?Iri $event,
        public ?Error $error,
    ) {}

    public function created(): bool
    {
        return $this->status === 201;
    }
}
