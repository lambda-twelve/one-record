<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * A JSON-LD document reduced to its RDF graph plus the two things the graph
 * alone cannot tell: which node the document was about, and which context it
 * used (kept so a response can be written back in the partner's own terms).
 */
final readonly class ExpandedDocument
{
    public function __construct(
        public Graph $graph,
        public Iri|BlankNode $root,
        public Context $context,
    ) {}

    public function rootIri(): ?Iri
    {
        return $this->root instanceof Iri ? $this->root : null;
    }

    /**
     * @return list<string> rdf:type IRIs of the root node, sorted
     */
    public function rootTypes(): array
    {
        $types = array_map(static fn(Iri $type): string => $type->value, $this->graph->typesOf($this->root));
        sort($types, SORT_STRING);

        return $types;
    }
}
