<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

/**
 * Ids for the resources the server creates itself: action requests, events,
 * objects posted by partners, outbound notifications. The default mints
 * random UUIDs; a host may prefer ULIDs or database-issued ids.
 */
interface IdGenerator
{
    /**
     * A new id, unique on this server and safe in a URL path segment.
     */
    public function next(): string;
}
