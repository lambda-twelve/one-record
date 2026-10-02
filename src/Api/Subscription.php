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
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:Subscription: who wants to be told about what. Immutable by spec; to
 * change one, revoke the request and make a new one.
 */
final readonly class Subscription
{
    /**
     * @param non-empty-list<SubscriptionEventType> $eventTypes
     * @param list<string> $contentTypes
     */
    public function __construct(
        public Iri $subscriber,
        public TopicType $topicType,
        public string $topic,
        public array $eventTypes,
        public bool $sendLogisticsObjectBody = false,
        public bool $notifyRequestStatusChange = false,
        public array $contentTypes = ['application/ld+json'],
        public ?string $description = null,
        public ?DateTimeImmutable $expiresAt = null,
        public ?Iri $id = null,
    ) {
        if ($eventTypes === []) {
            throw InvalidDocument::because('Invalid resource', 'A subscription must include at least one event type.', Api::includeSubscriptionEventType);
        }
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
        if (!\in_array(Api::Subscription, $expanded->rootTypes(), true)) {
            throw InvalidDocument::because('Invalid resource', 'The body is not an api:Subscription.');
        }

        return self::readNode($expanded->graph, $expanded->root);
    }

    public static function readNode(Graph $graph, Iri|BlankNode $node): self
    {
        $subscriber = Nodes::iri($graph, $node, Api::hasSubscriber)
            ?? throw InvalidDocument::because('Invalid resource', 'api:hasSubscriber must reference the subscriber organisation.', Api::hasSubscriber);
        $topicTypeIri = Nodes::iri($graph, $node, Api::hasTopicType);
        $topicType = $topicTypeIri !== null ? TopicType::tryFromString($topicTypeIri->value) : null;
        if ($topicType === null) {
            throw InvalidDocument::because('Invalid resource', 'api:hasTopicType must be LOGISTICS_OBJECT_IDENTIFIER or LOGISTICS_OBJECT_TYPE.', Api::hasTopicType);
        }
        $topic = Nodes::string($graph, $node, Api::hasTopic);
        if ($topic === null || preg_match('/^[a-z][a-z0-9+.-]*:/i', $topic) !== 1) {
            throw InvalidDocument::because('Invalid resource', 'api:hasTopic must be a URI.', Api::hasTopic);
        }
        $eventTypes = [];
        foreach (Nodes::iris($graph, $node, Api::includeSubscriptionEventType) as $iri) {
            $type = SubscriptionEventType::tryFromString($iri->value);
            if ($type === null) {
                throw InvalidDocument::because('Invalid resource', \sprintf('"%s" is not a subscription event type.', $iri->value), Api::includeSubscriptionEventType);
            }
            $eventTypes[$type->value] = $type;
        }
        if ($eventTypes === []) {
            throw InvalidDocument::because('Invalid resource', 'A subscription must include at least one event type.', Api::includeSubscriptionEventType);
        }
        $contentTypes = Nodes::strings($graph, $node, Api::hasContentType);

        return new self(
            $subscriber,
            $topicType,
            $topic,
            array_values($eventTypes),
            Nodes::bool($graph, $node, Api::sendLogisticsObjectBody) ?? false,
            Nodes::bool($graph, $node, Api::notifyRequestStatusChange) ?? false,
            $contentTypes === [] ? ['application/ld+json'] : $contentTypes,
            Nodes::string($graph, $node, Api::hasDescription),
            Nodes::dateTime($graph, $node, Api::expiresAt),
            $node instanceof Iri ? $node : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(): array
    {
        return ['@context' => Nodes::context(), ...$this->node()];
    }

    /**
     * @return array<string, mixed>
     */
    public function node(): array
    {
        $node = ['@type' => 'api:Subscription'];
        if ($this->id !== null) {
            $node['@id'] = $this->id->value;
        }
        $node['api:hasContentType'] = \count($this->contentTypes) === 1 ? $this->contentTypes[0] : $this->contentTypes;
        if ($this->description !== null) {
            $node['api:hasDescription'] = $this->description;
        }
        if ($this->expiresAt !== null) {
            $node['api:expiresAt'] = Nodes::dateTimeValue($this->expiresAt);
        }
        $node['api:hasSubscriber'] = Nodes::ref($this->subscriber);
        $node['api:hasTopicType'] = Nodes::ref(Nodes::compact($this->topicType->value));
        $node['api:includeSubscriptionEventType'] = array_map(static fn(SubscriptionEventType $t): array => Nodes::ref(Nodes::compact($t->value)), $this->eventTypes);
        $node['api:hasTopic'] = Nodes::anyUri($this->topic);
        if ($this->sendLogisticsObjectBody) {
            $node['api:sendLogisticsObjectBody'] = true;
        }
        if ($this->notifyRequestStatusChange) {
            $node['api:notifyRequestStatusChange'] = true;
        }

        return $node;
    }

    public function includes(SubscriptionEventType $type): bool
    {
        return \in_array($type, $this->eventTypes, true);
    }

    public function isExpiredAt(DateTimeImmutable $now): bool
    {
        return $this->expiresAt !== null && $this->expiresAt <= $now;
    }

    /**
     * Does this subscription cover the given object (by URI or by any of its types)?
     *
     * @param list<string> $types class IRIs of the object
     */
    public function covers(Iri $logisticsObject, array $types): bool
    {
        return match ($this->topicType) {
            TopicType::Identifier => $this->topic === $logisticsObject->value,
            TopicType::Type => \in_array($this->topic, $types, true),
        };
    }
}
