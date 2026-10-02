<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\JsonLd\Writer;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:Notification: what one node tells another about a created or updated
 * object, a received event, or the status of an action request.
 */
final readonly class Notification
{
    /**
     * @param list<string> $changedProperties property IRIs
     * @param list<Iri> $logisticsEvents
     * @param ?LogisticsObject $body the full object, when the subscription asked for it
     */
    public function __construct(
        public NotificationEventType $eventType,
        public ?Iri $logisticsObject = null,
        public ?string $logisticsObjectType = null,
        public ?Iri $triggeredBy = null,
        public array $changedProperties = [],
        public array $logisticsEvents = [],
        public ?LogisticsObject $body = null,
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
        if (!\in_array(Api::Notification, $expanded->rootTypes(), true)) {
            throw InvalidDocument::because('Invalid resource', 'The body is not an api:Notification.');
        }
        $graph = $expanded->graph;
        $root = $expanded->root;
        $typeIri = Nodes::iri($graph, $root, Api::hasEventType);
        $eventType = $typeIri !== null ? NotificationEventType::tryFromString($typeIri->value) : null;
        if ($eventType === null) {
            throw InvalidDocument::because('Invalid resource', 'api:hasEventType must be a notification event type.', Api::hasEventType);
        }
        $objectIri = Nodes::iri($graph, $root, Api::hasLogisticsObject);
        $body = null;
        if ($objectIri !== null) {
            // Anything said about the object beyond its URI is the embedded body (sendLogisticsObjectBody).
            $about = $graph->about($objectIri);
            if ($about !== []) {
                $sub = new Graph();
                $seen = [];
                self::collect($graph, $objectIri, $sub, $seen);
                $body = new LogisticsObject($objectIri, $sub);
            }
        }
        $objectType = Nodes::string($graph, $root, Api::hasLogisticsObjectType);
        if ($objectType === null && $body !== null) {
            $objectType = $body->mostSpecificType();
        }

        return new self(
            $eventType,
            $objectIri,
            $objectType,
            Nodes::iri($graph, $root, Api::isTriggeredBy),
            Nodes::strings($graph, $root, Api::hasChangedProperty),
            Nodes::iris($graph, $root, Api::hasLogisticsEvent),
            $body,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(): array
    {
        $node = ['@context' => Nodes::context(), '@type' => 'api:Notification', 'api:hasEventType' => Nodes::ref(Nodes::compact($this->eventType->value))];
        if ($this->logisticsObject !== null) {
            if ($this->body !== null) {
                $embedded = (new Writer())->write($this->body->graph, $this->body->iri, \LambdaTwelve\OneRecord\JsonLd\Context::oneRecord(), includeContext: false);
                $node['api:hasLogisticsObject'] = $embedded;
            } else {
                $node['api:hasLogisticsObject'] = Nodes::ref($this->logisticsObject);
            }
        }
        if ($this->logisticsObjectType !== null) {
            $node['api:hasLogisticsObjectType'] = Nodes::anyUri($this->logisticsObjectType);
        }
        if ($this->triggeredBy !== null) {
            $node['api:isTriggeredBy'] = Nodes::ref($this->triggeredBy);
        }
        if ($this->changedProperties !== []) {
            $node['api:hasChangedProperty'] = array_map(Nodes::anyUri(...), $this->changedProperties);
        }
        if ($this->logisticsEvents !== []) {
            $refs = array_map(static fn(Iri $i): array => Nodes::ref($i), $this->logisticsEvents);
            $node['api:hasLogisticsEvent'] = \count($refs) === 1 ? $refs[0] : $refs;
        }

        return $node;
    }

    /**
     * One visited set for the whole walk: a set per branch would terminate cycles but
     * revisit every shared descendant, which is exponential on a diamond-shaped graph (AR-004).
     *
     * @param array<string, true> $seen
     */
    private static function collect(Graph $graph, Iri|\LambdaTwelve\OneRecord\Rdf\BlankNode $node, Graph $into, array &$seen): void
    {
        $seen[$node->toNTriples()] = true;
        foreach ($graph->about($node) as $triple) {
            $into->add(new Triple($triple->subject, $triple->predicate, $triple->object));
            $object = $triple->object;
            if (($object instanceof Iri || $object instanceof \LambdaTwelve\OneRecord\Rdf\BlankNode) && !isset($seen[$object->toNTriples()])) {
                self::collect($graph, $object, $into, $seen);
            }
        }
    }
}
