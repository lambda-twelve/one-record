<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use DateTimeImmutable;
use InvalidArgumentException;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiFeatures;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LogicException;

/**
 * api:ActionRequest and its four subclasses: one organisation asks another
 * for something (a change, a subscription, access, a verification) and the
 * holder decides. Immutable; status changes produce a new instance so the
 * history the 2.3 spec asks for is a by-product.
 */
final readonly class ActionRequest
{
    /**
     * @param list<Error> $errors
     * @param list<RequestStatusEntry> $history previous statuses, oldest first (never the current one)
     */
    public function __construct(
        public Iri $iri,
        public ActionRequestType $type,
        public Change|Subscription|AccessDelegation|Verification $payload,
        public Iri $requestedBy,
        public DateTimeImmutable $requestedAt,
        public RequestStatus $status = RequestStatus::Pending,
        public ?DateTimeImmutable $statusSince = null,
        public array $history = [],
        public array $errors = [],
        public ?Iri $revokedBy = null,
        public ?DateTimeImmutable $revokedAt = null,
    ) {
        $expected = match (true) {
            $payload instanceof Change => ActionRequestType::Change,
            $payload instanceof Subscription => ActionRequestType::Subscription,
            $payload instanceof AccessDelegation => ActionRequestType::AccessDelegation,
            $payload instanceof Verification => ActionRequestType::Verification,
        };
        if ($expected !== $type) {
            throw new InvalidArgumentException(\sprintf('A %s cannot carry a %s.', $type->name, $payload::class));
        }
    }

    public static function create(Iri $iri, Change|Subscription|AccessDelegation|Verification $payload, Iri $requestedBy, DateTimeImmutable $at): self
    {
        $type = match (true) {
            $payload instanceof Change => ActionRequestType::Change,
            $payload instanceof Subscription => ActionRequestType::Subscription,
            $payload instanceof AccessDelegation => ActionRequestType::AccessDelegation,
            $payload instanceof Verification => ActionRequestType::Verification,
        };

        return new self($iri, $type, $payload, $requestedBy, $at, RequestStatus::Pending, $at);
    }

    public function notifyRequestStatusChange(): bool
    {
        return $this->payload->notifyRequestStatusChange;
    }

    /**
     * The logistics objects this request concerns (none for a type subscription).
     *
     * @return list<Iri>
     */
    public function logisticsObjects(): array
    {
        return match (true) {
            $this->payload instanceof Change, $this->payload instanceof Verification => [$this->payload->logisticsObject],
            $this->payload instanceof AccessDelegation => $this->payload->logisticsObjects,
            $this->payload instanceof Subscription => $this->payload->topicType === TopicType::Identifier ? [new Iri($this->payload->topic)] : [],
        };
    }

    public function lastModified(): DateTimeImmutable
    {
        return $this->statusSince ?? $this->requestedAt;
    }

    public function canTransitionTo(RequestStatus $next): bool
    {
        return $this->status->canTransitionTo($next, $this->type);
    }

    /**
     * @param list<Error> $errors
     */
    public function withStatus(RequestStatus $next, DateTimeImmutable $at, ?Iri $changedBy = null, array $errors = []): self
    {
        if (!$this->canTransitionTo($next)) {
            throw new LogicException(\sprintf('A %s cannot go from %s to %s.', $this->type->name, $this->status->shortName(), $next->shortName()));
        }

        return new self(
            $this->iri,
            $this->type,
            $this->payload,
            $this->requestedBy,
            $this->requestedAt,
            $next,
            $at,
            [...$this->history, new RequestStatusEntry($this->status, $this->statusSince ?? $this->requestedAt, $changedBy)],
            [...$this->errors, ...$errors],
            $next === RequestStatus::Revoked ? $changedBy : $this->revokedBy,
            $next === RequestStatus::Revoked ? $at : $this->revokedAt,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(ApiVersion $version): array
    {
        return [
            '@context' => [...Nodes::context(), 'api:hasDatatype' => ['@type' => 'xsd:anyURI'], 'api:p' => ['@type' => 'xsd:anyURI'], 'api:hasProperty' => ['@type' => 'xsd:anyURI'], 'api:hasResource' => ['@type' => 'xsd:anyURI']],
            ...$this->node($version),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function node(ApiVersion $version): array
    {
        $payload = match (true) {
            $this->payload instanceof Change => $this->payload->toJsonLd(),
            $this->payload instanceof Subscription => $this->payload->node(),
            $this->payload instanceof AccessDelegation => $this->payload->node($version),
            $this->payload instanceof Verification => $this->payload->node($version),
        };
        unset($payload['@context']);

        $node = [
            '@type' => Nodes::compact($this->type->value),
            '@id' => $this->iri->value,
            Nodes::compact($this->type->payloadProperty()) => $payload,
            'api:isRequestedBy' => Nodes::ref($this->requestedBy),
            'api:isRequestedAt' => Nodes::dateTimeValue($this->requestedAt),
            'api:hasRequestStatus' => Nodes::ref(Nodes::compact($this->status->value)),
        ];
        if ($this->errors !== []) {
            $node['api:hasError'] = ErrorDocument::nodes($this->errors, $version);
        }
        if ($this->revokedBy !== null) {
            $node['api:isRevokedBy'] = Nodes::ref($this->revokedBy);
        }
        if ($this->revokedAt !== null) {
            $node['api:isRevokedAt'] = Nodes::dateTimeValue($this->revokedAt);
        }
        if (ApiFeatures::available($version, ApiFeatures::REQUEST_STATUS_SINCE)) {
            $node['api:hasRequestStatusSince'] = Nodes::dateTimeValue($this->statusSince ?? $this->requestedAt);
        }
        if ($this->history !== [] && ApiFeatures::available($version, ApiFeatures::REQUEST_STATUS_HISTORY)) {
            $node['api:hasRequestStatusHistory'] = array_map(static function (RequestStatusEntry $entry): array {
                $item = ['@type' => 'api:RequestStatusEntry', 'api:hasRequestStatus' => Nodes::ref(Nodes::compact($entry->status->value)), 'api:hasRequestStatusSince' => Nodes::dateTimeValue($entry->since)];
                if ($entry->changedBy !== null) {
                    $item['api:isChangedBy'] = Nodes::ref($entry->changedBy);
                }

                return $item;
            }, $this->history);
        }

        return $node;
    }

    /**
     * Reads an action request as a partner's server returns it (the client side).
     *
     * @param string|array<string, mixed>|ExpandedDocument $document
     */
    public static function fromJsonLd(string|array|ExpandedDocument $document): self
    {
        try {
            $expanded = $document instanceof ExpandedDocument ? $document : JsonLd::expand($document);
        } catch (JsonLdException $e) {
            throw InvalidDocument::because('Invalid body request', $e->getMessage());
        }
        $graph = $expanded->graph;
        $root = $expanded->root;
        $type = null;
        foreach ($expanded->rootTypes() as $typeIri) {
            $type = ActionRequestType::tryFrom($typeIri) ?? $type;
        }
        if ($type === null || !$root instanceof Iri) {
            throw InvalidDocument::because('Invalid resource', 'The body is not an identified api:ActionRequest.');
        }
        $payloadNode = Nodes::node($graph, $root, $type->payloadProperty())
            ?? throw InvalidDocument::because('Invalid resource', 'The action request has no payload.', $type->payloadProperty());
        $payload = match ($type) {
            ActionRequestType::Change => Change::fromJsonLd(new ExpandedDocument($graph, $payloadNode, $expanded->context)),
            ActionRequestType::Subscription => Subscription::readNode($graph, $payloadNode),
            ActionRequestType::AccessDelegation => AccessDelegation::readNode($graph, $payloadNode),
            ActionRequestType::Verification => Verification::readNode($graph, $payloadNode),
        };
        $statusIri = Nodes::iri($graph, $root, Api::hasRequestStatus);
        $status = $statusIri !== null ? RequestStatus::tryFromString($statusIri->value) : null;
        $requestedAt = Nodes::dateTime($graph, $root, Api::isRequestedAt) ?? new DateTimeImmutable('@0');
        $history = [];
        foreach (Nodes::nodes($graph, $root, Api::hasRequestStatusHistory) as $entry) {
            $entryStatusIri = Nodes::iri($graph, $entry, Api::hasRequestStatus);
            $entryStatus = $entryStatusIri !== null ? RequestStatus::tryFromString($entryStatusIri->value) : null;
            $since = Nodes::dateTime($graph, $entry, Api::hasRequestStatusSince);
            if ($entryStatus !== null && $since !== null) {
                $history[] = new RequestStatusEntry($entryStatus, $since, Nodes::iri($graph, $entry, Api::isChangedBy));
            }
        }
        usort($history, static fn(RequestStatusEntry $a, RequestStatusEntry $b): int => $a->since <=> $b->since);

        return new self(
            $root,
            $type,
            $payload,
            Nodes::iri($graph, $root, Api::isRequestedBy) ?? throw InvalidDocument::because('Invalid resource', 'api:isRequestedBy is required.', Api::isRequestedBy),
            $requestedAt,
            $status ?? RequestStatus::Pending,
            Nodes::dateTime($graph, $root, Api::hasRequestStatusSince),
            $history,
            ErrorDocument::readFrom($graph, $root, Api::hasError),
            Nodes::iri($graph, $root, Api::isRevokedBy),
            Nodes::dateTime($graph, $root, Api::isRevokedAt),
        );
    }
}
