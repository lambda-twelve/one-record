<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * The spec's recommended scheme: `internal:<uuid5>`, derived from the parent
 * object's URI and a discriminator, so the same input never yields two ids
 * and the ids are unique across the server.
 */
final class Uuid5EmbeddedIdMinter implements EmbeddedIdMinter
{
    public function mint(Iri $parent, string $discriminator): Iri
    {
        return new Iri(Namespaces::EMBEDDED . Uuid::v5(Uuid::NAMESPACE_URL, $parent->value . '#' . $discriminator));
    }
}
