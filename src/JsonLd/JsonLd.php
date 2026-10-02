<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Entry points for the JSON-LD subset: text or decoded array in, graph out,
 * and back.
 */
final class JsonLd
{
    private function __construct() {}

    /**
     * @param string|array<string, mixed> $document JSON text or a decoded object
     */
    public static function expand(string|array $document): ExpandedDocument
    {
        $decoded = \is_string($document) ? Json::decodeObject($document) : $document;

        return (new Expander())->expand($decoded);
    }

    /**
     * @return array<string, mixed>
     */
    public static function compact(Graph $graph, Iri|BlankNode $root, ?Context $context = null): array
    {
        return (new Writer())->write($graph, $root, $context ?? Context::oneRecord());
    }

    public static function compactToJson(Graph $graph, Iri|BlankNode $root, ?Context $context = null, bool $pretty = true): string
    {
        return Json::encode(self::compact($graph, $root, $context), $pretty);
    }
}
