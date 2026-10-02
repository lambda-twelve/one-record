<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * api:Collection: how a list of logistics events is returned. The spec's
 * JSON-LD rule applies: no hasItem for an empty list, a single object for one
 * item, an array for several.
 */
final class Collection
{
    private function __construct() {}

    /**
     * @param list<array<string, mixed>> $items compacted nodes without @context
     * @return array<string, mixed>
     */
    public static function write(Iri $id, array $items): array
    {
        $node = ['@context' => Nodes::context(), '@id' => $id->value, '@type' => 'api:Collection', 'api:hasTotalItems' => \count($items)];
        if (\count($items) === 1) {
            $node['api:hasItem'] = $items[0];
        } elseif ($items !== []) {
            $node['api:hasItem'] = $items;
        }

        return $node;
    }
}
