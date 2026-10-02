<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Stable identifiers for embedded objects (Value, Dimensions, Party, ...).
 *
 * The spec requires every embedded object to have an id that never changes
 * during its lifetime, because change requests address embedded objects by
 * it. Ids are minted once, when the object enters a stored logistics object,
 * and kept in the stored graph from then on.
 */
interface EmbeddedIdMinter
{
    /**
     * @param Iri $parent the logistics object the embedded object belongs to
     * @param string $discriminator something unique for this embedded object within the parent and revision
     */
    public function mint(Iri $parent, string $discriminator): Iri;
}
