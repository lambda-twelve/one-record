<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\JsonLd\Writer;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;

/**
 * cargo:LogisticsEvent: an immutable status update attached to one logistics
 * object, with its own URI below the object's. Neither a logistics object nor
 * an embedded object, as the spec says.
 */
final readonly class LogisticsEvent
{
    public function __construct(
        public Iri $iri,
        public Iri $logisticsObject,
        public Graph $graph,
        public DateTimeImmutable $created,
    ) {}

    /**
     * Rebuilds an event a store wrote with toJsonLd(): no validation, no
     * re-rooting beyond giving a blank root its URI. Stores use this; the
     * server uses fromJsonLd() for what partners post.
     *
     * @param string|array<string, mixed> $json
     */
    public static function fromStored(Iri $iri, Iri $logisticsObject, string|array $json, DateTimeImmutable $created): self
    {
        $document = JsonLd::expand($json, $iri);
        $graph = $document->graph;
        if (!$document->root->equals($iri)) {
            $renamed = new Graph();
            foreach ($graph as $triple) {
                $renamed->add(new Triple(
                    $triple->subject->equals($document->root) ? $iri : $triple->subject,
                    $triple->predicate,
                    $triple->object->equals($document->root) ? $iri : $triple->object,
                ));
            }
            $graph = $renamed;
        }

        return new self($iri, $logisticsObject, $graph, $created);
    }

    /**
     * Reads a posted event body and gives it its URI under the object.
     *
     * @param string|array<string, mixed> $json
     */
    public static function fromJsonLd(string|array $json, Iri $iri, Iri $logisticsObject, DateTimeImmutable $created): self
    {
        try {
            $document = JsonLd::expand($json);
        } catch (JsonLdException $e) {
            throw new ModelException($e->getMessage(), previous: $e);
        }
        $types = $document->rootTypes();
        if ($types === [] || !\in_array(Cargo::LogisticsEvent, $types, true) && !\in_array(Cargo::StatusUpdateEvent, $types, true)) {
            throw new ModelException('The body is not a cargo:LogisticsEvent.');
        }
        $graph = new Graph();
        foreach ($document->graph as $triple) {
            $subject = $triple->subject->equals($document->root) ? $iri : $triple->subject;
            $object = $triple->object->equals($document->root) ? $iri : $triple->object;
            $graph->add(new Triple($subject, $triple->predicate, $object));
        }
        $event = new self($iri, $logisticsObject, $graph, $created);
        if ($event->eventDate() === null) {
            throw new ModelException('Every logistics event must have a cargo:eventDate.');
        }

        return $event;
    }

    public function eventDate(): ?DateTimeImmutable
    {
        return Nodes::dateTime($this->graph, $this->iri, Cargo::eventDate);
    }

    public function creationDate(): ?DateTimeImmutable
    {
        return Nodes::dateTime($this->graph, $this->iri, Cargo::creationDate);
    }

    /**
     * The event code as written: a code-list IRI (…/StatusCode#DEP), a
     * CodeListElement's cargo:code, or a plain string. Filters match on it.
     */
    public function eventCode(): ?string
    {
        $term = $this->graph->firstObject($this->iri, Cargo::eventCode);
        if ($term instanceof Iri) {
            return $term->value;
        }
        if ($term instanceof BlankNode) {
            return Nodes::string($this->graph, $term, Cargo::code);
        }

        return $term === null ? null : Nodes::string($this->graph, $this->iri, Cargo::eventCode);
    }

    public function matchesCode(string $filter): bool
    {
        $code = $this->eventCode();

        return $code !== null && ($code === $filter || str_ends_with($code, '#' . $filter) || str_ends_with($code, '/' . $filter));
    }

    /**
     * @return list<string>
     */
    public function types(): array
    {
        $types = array_map(static fn(Iri $t): string => $t->value, $this->graph->typesOf($this->iri));
        sort($types, SORT_STRING);

        return $types;
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(?Context $context = null, bool $includeContext = true): array
    {
        return (new Writer())->write($this->graph, $this->iri, $context ?? Context::oneRecord(), $includeContext);
    }
}
