<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Rdf\Iri;

// Fixture for the layer rule: Model may use JsonLd and Rdf, not Api.
final class UpwardDependency
{
    public function fine(): string
    {
        return Nodes::compact(Iri::class);
    }

    public function wrong(): Error
    {
        return Error::of('x');
    }
}
