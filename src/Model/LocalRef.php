<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * A reference to another object of the same graph by its local key, written
 * as the IRI `local:<key>` until the graph is resolved and real URIs minted.
 */
final readonly class LocalRef
{
    public function __construct(public string $key)
    {
        if ($key === '' || preg_match('/^[A-Za-z0-9][A-Za-z0-9_.-]*$/', $key) !== 1) {
            throw new ModelException(\sprintf('"%s" is not a valid local key (letters, digits, "_", "-", ".").', $key));
        }
    }

    public static function to(string $key): self
    {
        return new self($key);
    }

    public function iri(): Iri
    {
        return new Iri(Namespaces::LOCAL . $this->key);
    }

    public static function isLocal(Iri $iri): bool
    {
        return str_starts_with($iri->value, Namespaces::LOCAL);
    }

    public static function keyOf(Iri $iri): string
    {
        if (!self::isLocal($iri)) {
            throw new ModelException(\sprintf('"%s" is not a local reference.', $iri->value));
        }

        return substr($iri->value, \strlen(Namespaces::LOCAL));
    }
}
