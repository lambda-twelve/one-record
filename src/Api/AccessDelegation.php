<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiFeatures;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:AccessDelegation: permissions on logistics objects for organisations.
 * 2.2 allowed several delegates in one request; 2.3 requires exactly one and
 * adds an expiry. Both shapes are read; the negotiated version decides what
 * is written and whether several delegates are accepted.
 */
final readonly class AccessDelegation
{
    /**
     * @param non-empty-list<Permission> $permissions
     * @param non-empty-list<Iri> $delegates the organisations access is requested for (api:isRequestedFor)
     * @param non-empty-list<Iri> $logisticsObjects
     */
    public function __construct(
        public array $permissions,
        public array $delegates,
        public array $logisticsObjects,
        public ?string $description = null,
        public bool $notifyRequestStatusChange = false,
        public ?DateTimeImmutable $expiresAt = null,
    ) {}

    /**
     * @param string|array<string, mixed>|ExpandedDocument $document
     */
    public static function fromJsonLd(string|array|ExpandedDocument $document, ApiVersion $version = ApiVersion::V2_2_0): self
    {
        try {
            $expanded = $document instanceof ExpandedDocument ? $document : JsonLd::expand($document);
        } catch (JsonLdException $e) {
            throw InvalidDocument::because('Invalid body request', $e->getMessage());
        }
        if (!\in_array(Api::AccessDelegation, $expanded->rootTypes(), true)) {
            throw InvalidDocument::because('Invalid resource', 'The body is not an api:AccessDelegation.');
        }

        return self::readNode($expanded->graph, $expanded->root, $version);
    }

    public static function readNode(Graph $graph, Iri|BlankNode $node, ApiVersion $version = ApiVersion::V2_2_0): self
    {
        $permissions = [];
        foreach (Nodes::iris($graph, $node, Api::hasPermission) as $iri) {
            $permission = Permission::tryFromString($iri->value)
                ?? throw InvalidDocument::because('Invalid resource', \sprintf('"%s" is not a permission.', $iri->value), Api::hasPermission);
            $permissions[$permission->value] = $permission;
        }
        if ($permissions === []) {
            throw InvalidDocument::because('Invalid resource', 'An access delegation needs at least one permission.', Api::hasPermission);
        }
        $delegates = Nodes::iris($graph, $node, Api::isRequestedFor);
        if ($delegates === []) {
            throw InvalidDocument::because('Invalid resource', 'api:isRequestedFor must name the organisation(s) to grant access to.', Api::isRequestedFor);
        }
        if (\count($delegates) > 1 && ApiFeatures::available($version, ApiFeatures::SINGLE_DELEGATE)) {
            throw InvalidDocument::because('Invalid resource', 'api:isRequestedFor names exactly one organisation in API ' . $version->value . '.', Api::isRequestedFor);
        }
        $objects = Nodes::iris($graph, $node, Api::hasLogisticsObject);
        if ($objects === []) {
            throw InvalidDocument::because('Invalid resource', 'api:hasLogisticsObject must name at least one logistics object.', Api::hasLogisticsObject);
        }

        return new self(
            array_values($permissions),
            $delegates,
            $objects,
            Nodes::string($graph, $node, Api::hasDescription),
            Nodes::bool($graph, $node, Api::notifyRequestStatusChange) ?? false,
            Nodes::dateTime($graph, $node, Api::expiresAt),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(?ApiVersion $version = null): array
    {
        $version ??= ApiVersion::latest();

        return ['@context' => Nodes::context(), ...$this->node($version)];
    }

    /**
     * @return array<string, mixed>
     */
    public function node(ApiVersion $version): array
    {
        $node = ['@type' => 'api:AccessDelegation'];
        if ($this->description !== null) {
            $node['api:hasDescription'] = $this->description;
        }
        $node['api:hasPermission'] = array_map(static fn(Permission $p): array => Nodes::ref(Nodes::compact($p->value)), $this->permissions);
        $node['api:isRequestedFor'] = array_map(static fn(Iri $i): array => Nodes::ref($i), $this->delegates);
        $node['api:notifyRequestStatusChange'] = $this->notifyRequestStatusChange;
        $node['api:hasLogisticsObject'] = array_map(static fn(Iri $i): array => Nodes::ref($i), $this->logisticsObjects);
        if ($this->expiresAt !== null && ApiFeatures::available($version, ApiFeatures::ACCESS_DELEGATION_EXPIRY)) {
            $node['api:expiresAt'] = Nodes::dateTimeValue($this->expiresAt);
        }

        return $node;
    }

    public function isExpiredAt(DateTimeImmutable $now): bool
    {
        return $this->expiresAt !== null && $this->expiresAt <= $now;
    }
}
