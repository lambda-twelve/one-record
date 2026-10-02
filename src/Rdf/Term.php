<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Rdf;

/**
 * A node in an RDF graph: an IRI, a blank node or a literal.
 *
 * ONE Record is RDF underneath its JSON-LD, and two documents are "the same"
 * when their graphs are isomorphic, not when their JSON is equal. Everything
 * that compares, diffs or patches logistics objects works on these terms.
 */
interface Term
{
    /**
     * A stable textual form used for sorting and hashing: N-Triples syntax.
     */
    public function toNTriples(): string;

    public function equals(Term $other): bool;
}
