<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiFeatures;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:Error as JSON-LD: the body of every 4xx/5xx response, and the errors
 * embedded in action requests and verifications. Severity is a 2.3 addition
 * and is left out when 2.2 was negotiated.
 */
final class ErrorDocument
{
    private function __construct() {}

    /**
     * @return array<string, mixed>
     */
    public static function write(Error $error, ApiVersion $version, string $language = 'en-US'): array
    {
        return [
            '@context' => [...Nodes::context(), 'api:hasResource' => ['@type' => 'xsd:anyURI'], 'api:hasProperty' => ['@type' => 'xsd:anyURI'], '@language' => $language],
            ...self::node($error, $version),
        ];
    }

    /**
     * The error without its own @context, for embedding in another document.
     *
     * @return array<string, mixed>
     */
    public static function node(Error $error, ApiVersion $version): array
    {
        $node = ['@type' => 'api:Error', 'api:hasTitle' => $error->title];
        if (ApiFeatures::available($version, ApiFeatures::ERROR_SEVERITY)) {
            $node['api:hasSeverity'] = Nodes::ref(Nodes::compact($error->severity->value));
        }
        $details = [];
        foreach ($error->details as $detail) {
            $item = ['@type' => 'api:ErrorDetail'];
            if ($detail->code !== null) {
                $item['api:hasCode'] = $detail->code;
            }
            if ($detail->message !== null) {
                $item['api:hasMessage'] = $detail->message;
            }
            if ($detail->property !== null) {
                $item['api:hasProperty'] = $detail->property;
            }
            if ($detail->resource !== null) {
                $item['api:hasResource'] = $detail->resource;
            }
            $details[] = $item;
        }
        if ($details !== []) {
            $node['api:hasErrorDetail'] = $details;
        }

        return $node;
    }

    /**
     * @param list<Error> $errors
     * @return list<array<string, mixed>>
     */
    public static function nodes(array $errors, ApiVersion $version): array
    {
        return array_map(static fn(Error $e): array => self::node($e, $version), $errors);
    }

    /**
     * Reads the api:Error nodes referenced from $node by $predicate.
     *
     * @return list<Error>
     */
    public static function readFrom(Graph $graph, Iri|BlankNode $node, string $predicate): array
    {
        $errors = [];
        foreach (Nodes::nodes($graph, $node, $predicate) as $errorNode) {
            $errors[] = self::readNode($graph, $errorNode);
        }

        return $errors;
    }

    public static function readNode(Graph $graph, Iri|BlankNode $node): Error
    {
        $title = Nodes::string($graph, $node, Api::hasTitle) ?? 'Error';
        $severityIri = Nodes::iri($graph, $node, Api::hasSeverity);
        $severity = $severityIri !== null ? (Severity::tryFrom($severityIri->value) ?? Severity::Error) : Severity::Error;
        $details = [];
        foreach (Nodes::nodes($graph, $node, Api::hasErrorDetail) as $detailNode) {
            $details[] = new ErrorDetail(
                Nodes::string($graph, $detailNode, Api::hasCode),
                Nodes::string($graph, $detailNode, Api::hasMessage),
                Nodes::string($graph, $detailNode, Api::hasProperty),
                Nodes::string($graph, $detailNode, Api::hasResource),
            );
        }

        return new Error($title, $details, $severity);
    }

    /**
     * Reads a standalone api:Error body (an HTTP error response from a partner).
     *
     * @param string|array<string, mixed> $json
     */
    public static function read(string|array $json): ?Error
    {
        try {
            $document = JsonLd::expand($json);
        } catch (JsonLdException) {
            return null;
        }
        if (!\in_array(Api::Error, $document->rootTypes(), true)) {
            return null;
        }

        return self::readNode($document->graph, $document->root);
    }

    public static function language(): string
    {
        return 'en-US';
    }

    /** @internal keeps Namespaces imported for the context of embedded errors */
    public static function xsd(): string
    {
        return Namespaces::XSD;
    }
}
