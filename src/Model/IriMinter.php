<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * How a host forms the public URI of a logistics object it publishes.
 *
 * The spec fixes the shape ({base}/logistics-objects/{id}) and leaves the id
 * to the implementer. A host that wants ids derived from its own records
 * (so a republished graph gets the same URIs partners already hold)
 * implements this; the defaults mint UUIDs under a base URL.
 *
 * @param list<string> $types class IRIs of the object, in case ids are type-specific
 */
interface IriMinter
{
    /**
     * @param string $localKey the key the object has inside the graph being published
     * @param list<string> $types
     */
    public function mint(string $localKey, array $types): Iri;
}
