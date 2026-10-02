<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:Verification: a third party flags problems in a logistics object
 * without proposing new values. Posted to POST /logistics-objects/{id}.
 */
final readonly class Verification
{
    /**
     * @param non-empty-list<Error> $errors
     */
    public function __construct(
        public Iri $logisticsObject,
        public array $errors,
        public ?int $revision = null,
        public bool $notifyRequestStatusChange = false,
    ) {}

    /**
     * @param string|array<string, mixed>|ExpandedDocument $document
     */
    public static function fromJsonLd(string|array|ExpandedDocument $document): self
    {
        try {
            $expanded = $document instanceof ExpandedDocument ? $document : JsonLd::expand($document);
        } catch (JsonLdException $e) {
            throw InvalidDocument::because('Invalid body request', $e->getMessage());
        }
        if (!\in_array(Api::Verification, $expanded->rootTypes(), true)) {
            throw InvalidDocument::because('Invalid resource', 'The body is not an api:Verification.');
        }

        return self::readNode($expanded->graph, $expanded->root);
    }

    public static function readNode(Graph $graph, Iri|BlankNode $node): self
    {
        $target = Nodes::iri($graph, $node, Api::hasLogisticsObject)
            ?? throw InvalidDocument::because('Invalid resource', 'api:hasLogisticsObject must reference the logistics object.', Api::hasLogisticsObject);
        $errors = ErrorDocument::readFrom($graph, $node, Api::hasError);
        if ($errors === []) {
            throw InvalidDocument::because('Invalid resource', 'A verification must report at least one error.', Api::hasError);
        }

        return new self($target, $errors, Nodes::int($graph, $node, Api::hasRevision), Nodes::bool($graph, $node, Api::notifyRequestStatusChange) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(?ApiVersion $version = null): array
    {
        $version ??= ApiVersion::latest();

        return ['@context' => [...Nodes::context(), 'api:hasProperty' => ['@type' => 'xsd:anyURI'], 'api:hasResource' => ['@type' => 'xsd:anyURI']], ...$this->node($version)];
    }

    /**
     * @return array<string, mixed>
     */
    public function node(ApiVersion $version): array
    {
        $node = ['@type' => 'api:Verification', 'api:hasLogisticsObject' => Nodes::ref($this->logisticsObject), 'api:hasError' => ErrorDocument::nodes($this->errors, $version)];
        if ($this->revision !== null) {
            $node['api:hasRevision'] = Nodes::positiveInteger($this->revision);
        }
        if ($this->notifyRequestStatusChange) {
            $node['api:notifyRequestStatusChange'] = true;
        }

        return $node;
    }
}
