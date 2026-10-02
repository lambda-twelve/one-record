<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:ServerInformation: GET / on every ONE Record server. The client reads
 * it to learn which API versions a partner speaks; the server writes it from
 * its configuration.
 */
final readonly class ServerInformation
{
    /**
     * @param list<string> $apiVersions
     * @param list<string> $contentTypes
     * @param list<string> $languages
     * @param list<string> $ontologies unversioned ontology IRIs
     * @param list<string> $ontologyVersions versioned ontology IRIs
     * @param list<string> $encodings
     */
    public function __construct(
        public Iri $serverEndpoint,
        public Iri $dataHolder,
        public array $apiVersions,
        public array $contentTypes = ['application/ld+json'],
        public array $languages = ['en-US'],
        public array $ontologies = [Namespaces::CARGO_ONTOLOGY, Namespaces::API_ONTOLOGY],
        public array $ontologyVersions = [],
        public array $encodings = [],
        public ?string $dataHolderType = null,
        public ?Iri $id = null,
    ) {}

    /**
     * @param list<ApiVersion> $apiVersions
     * @param list<DataModelVersion> $dataModelVersions
     * @param list<string> $languages
     */
    public static function for(Iri $serverEndpoint, Iri $dataHolder, array $apiVersions, array $dataModelVersions, array $languages = ['en-US'], ?string $dataHolderType = null): self
    {
        $ontologyVersions = [];
        foreach ($dataModelVersions as $dm) {
            $ontologyVersions[] = $dm->ontologyVersionIri();
        }
        foreach ($apiVersions as $api) {
            $ontologyVersions[] = $api->ontologyVersionIri();
        }

        return new self(
            $serverEndpoint,
            $dataHolder,
            array_map(static fn(ApiVersion $v): string => $v->value, $apiVersions),
            languages: $languages,
            ontologyVersions: $ontologyVersions,
            dataHolderType: $dataHolderType,
        );
    }

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
        if (!\in_array(Api::ServerInformation, $expanded->rootTypes(), true)) {
            throw InvalidDocument::because('Invalid resource', 'The body is not api:ServerInformation.');
        }
        $graph = $expanded->graph;
        $root = $expanded->root;
        $holder = Nodes::iri($graph, $root, Api::hasDataHolder) ?? throw InvalidDocument::because('Invalid resource', 'api:hasDataHolder is required.', Api::hasDataHolder);
        $endpoint = Nodes::iri($graph, $root, Api::hasServerEndpoint) ?? ($root instanceof Iri ? $root : throw InvalidDocument::because('Invalid resource', 'api:hasServerEndpoint is required.', Api::hasServerEndpoint));
        $holderTypes = array_map(static fn(Iri $t): string => $t->value, $graph->typesOf($holder));

        return new self(
            $endpoint,
            $holder,
            Nodes::strings($graph, $root, Api::hasSupportedApiVersion),
            Nodes::strings($graph, $root, Api::hasSupportedContentType),
            Nodes::strings($graph, $root, Api::hasSupportedLanguage),
            Nodes::strings($graph, $root, Api::hasSupportedOntology),
            Nodes::strings($graph, $root, Api::hasSupportedOntologyVersion),
            Nodes::strings($graph, $root, Api::hasSupportedEncoding),
            $holderTypes[0] ?? null,
            $root instanceof Iri ? $root : null,
        );
    }

    /**
     * The highest API version both this server and the partner support, or null.
     *
     * @param list<ApiVersion> $ours
     */
    public function bestCommonApiVersion(array $ours): ?ApiVersion
    {
        $theirs = [];
        foreach ($this->apiVersions as $version) {
            $parsed = ApiVersion::tryFromString($version);
            if ($parsed !== null) {
                $theirs[$parsed->value] = $parsed;
            }
        }
        foreach (ApiVersion::allDescending() as $candidate) {
            if (\in_array($candidate, $ours, true) && isset($theirs[$candidate->value])) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(): array
    {
        $holder = Nodes::ref($this->dataHolder);
        if ($this->dataHolderType !== null) {
            $holder = ['@type' => Nodes::compact($this->dataHolderType), ...$holder];
        }
        $node = [
            '@context' => [...Nodes::context(), 'api:hasServerEndpoint' => ['@type' => 'xsd:anyURI'], 'api:hasSupportedOntology' => ['@type' => 'xsd:anyURI'], 'api:hasSupportedOntologyVersion' => ['@type' => 'xsd:anyURI']],
            '@id' => ($this->id ?? $this->serverEndpoint)->value,
            '@type' => 'api:ServerInformation',
            'api:hasDataHolder' => $holder,
            'api:hasServerEndpoint' => $this->serverEndpoint->value,
            'api:hasSupportedApiVersion' => $this->apiVersions,
            'api:hasSupportedContentType' => $this->contentTypes,
        ];
        if ($this->encodings !== []) {
            $node['api:hasSupportedEncoding'] = $this->encodings;
        }
        $node['api:hasSupportedLanguage'] = $this->languages;
        $node['api:hasSupportedOntology'] = $this->ontologies;
        if ($this->ontologyVersions !== []) {
            $node['api:hasSupportedOntologyVersion'] = $this->ontologyVersions;
        }

        return $node;
    }
}
