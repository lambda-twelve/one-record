<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Collection;
use LambdaTwelve\OneRecord\Api\InvalidDocument;
use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Model\ModelException;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Event\LogisticsEventReceived;
use LambdaTwelve\OneRecord\Server\Http\ContentNegotiation;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\Notification\Fanout;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\EventQuery;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * /logistics-objects/{id}/logistics-events: POST appends an immutable event
 * (and notifies subscribers), GET lists them as an api:Collection with the
 * spec's filters, HEAD gives the list's Last-Modified so clients can poll
 * cheaply.
 */
final class LogisticsEventsEndpoint extends AbstractEndpoint
{
    private const array SORTS = [EventQuery::SORT_CREATED_ASC, EventQuery::SORT_CREATED_DESC, EventQuery::SORT_EVENT_ASC, EventQuery::SORT_EVENT_DESC];

    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        return strtoupper($request->getMethod()) === 'POST'
            ? $this->create($request, $agent, $negotiated, $parameters['id'])
            : $this->list($request, $agent, $negotiated, $parameters['id']);
    }

    private function create(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, string $id): ResponseInterface
    {
        $stored = $this->requireObject($id, $agent, Action::PostLogisticsEvent);
        (new ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $json = $this->services->body->json($request);
        $eventIri = $this->services->config->logisticsEventIri($id, $this->services->ids->next());
        $now = $this->services->clock->now();
        try {
            $event = LogisticsEvent::fromJsonLd($json, $eventIri, $stored->object->iri, $now);
        } catch (ModelException $e) {
            throw HttpException::badRequest($e->getMessage(), null, $e->getPrevious() instanceof JsonLdException ? 'Invalid body request' : 'Invalid resource');
        }
        $this->validate($event, $stored->object->iri);

        $this->services->events->append($event);
        $this->services->dispatcher->dispatch(new LogisticsEventReceived($event, $agent));
        (new Fanout($this->services))->logisticsEventReceived($stored, $event);

        return $this->services->responder->empty(201, $negotiated, ['Location' => $eventIri->value, 'Type' => Cargo::LogisticsEvent]);
    }

    private function list(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, string $id): ResponseInterface
    {
        $stored = $this->requireObject($id, $agent, Action::ReadLogisticsEvent);
        $query = $this->eventQuery(self::query($request));
        $events = $this->services->events->query($stored->object->iri, $query);
        $context = Context::oneRecord();
        $items = array_map(static fn(LogisticsEvent $e): array => $e->toJsonLd($context, includeContext: false), $events);
        $collectionIri = new Iri($stored->object->iri->value . '/logistics-events');
        $lastModified = $this->services->events->lastModified($stored->object->iri) ?? $stored->createdAt;

        return $this->services->responder->jsonLd(200, Collection::write($collectionIri, $items), $negotiated, Api::Collection, ['Last-Modified' => Responder::httpDate($lastModified)], self::isHead($request));
    }

    /**
     * @param array<string, string> $query
     */
    private function eventQuery(array $query): EventQuery
    {
        $codes = [];
        foreach (explode(',', $query['event-code'] ?? '') as $code) {
            if (trim($code) !== '') {
                $codes[] = trim($code);
            }
        }
        $sort = $query['sort'] ?? EventQuery::SORT_CREATED_ASC;
        if (!\in_array($sort, self::SORTS, true)) {
            throw HttpException::invalidQuery(\sprintf('sort must be one of %s.', implode(', ', self::SORTS)), 'sort');
        }
        $limit = null;
        if (isset($query['limit']) && $query['limit'] !== '') {
            if (preg_match('/^\d+$/', $query['limit']) !== 1 || (int) $query['limit'] < 1) {
                throw HttpException::invalidQuery('limit must be a positive integer.', 'limit');
            }
            $limit = (int) $query['limit'];
        }
        $skip = 0;
        if (isset($query['skip']) && $query['skip'] !== '') {
            if (preg_match('/^\d+$/', $query['skip']) !== 1) {
                throw HttpException::invalidQuery('skip must be a non-negative integer.', 'skip');
            }
            $skip = (int) $query['skip'];
        }
        $time = static fn(string $name): ?DateTimeImmutable => isset($query[$name]) && trim($query[$name]) !== '' ? self::parseAtNamed($query[$name], $name) : null;

        return new EventQuery($codes, $time('created-after'), $time('created-before'), $time('occurred-after'), $time('occurred-before'), $sort, $limit, $skip);
    }

    private static function parseAtNamed(string $value, string $parameter): DateTimeImmutable
    {
        try {
            return self::parseAt($value);
        } catch (HttpException) {
            throw HttpException::invalidQuery(\sprintf('"%s" is not a timestamp in the form YYYYMMDDThhmmssZ.', $value), $parameter);
        }
    }

    private function validate(LogisticsEvent $event, Iri $object): void
    {
        self::validateEvent($this->services->vocabulary, $event, $object);
    }

    /**
     * The event must be a LogisticsEvent, say which object it is for (if it says
     * anything) and use properties LogisticsEvent accepts. Shared with the bulk
     * endpoint so both routes accept exactly the same events (AR-014).
     */
    public static function validateEvent(\LambdaTwelve\OneRecord\Vocabulary\Vocabulary $vocabulary, LogisticsEvent $event, Iri $object): void
    {
        $types = $event->types();
        $isEvent = static fn(string $type): bool => $type === Cargo::LogisticsEvent || $vocabulary->isSubclassOf($type, Cargo::LogisticsEvent);
        if (array_filter($types, $isEvent) === []) {
            throw new InvalidDocument('The body is not a cargo:LogisticsEvent.', [\LambdaTwelve\OneRecord\Api\Error::of('Invalid resource', '400', \sprintf('Expected a cargo:LogisticsEvent, got %s.', $types === [] ? 'no @type' : implode(', ', $types)), '@type')]);
        }
        foreach ($event->graph->about($event->iri) as $triple) {
            $predicate = $triple->predicate->value;
            if ($predicate === \LambdaTwelve\OneRecord\Rdf\Graph::RDF_TYPE) {
                continue;
            }
            if ($vocabulary->property($predicate) === null) {
                throw new InvalidDocument(\sprintf('"%s" is not a property of the ONE Record ontology.', $predicate), [\LambdaTwelve\OneRecord\Api\Error::of('Invalid resource', '400', \sprintf('"%s" is not a property of the ONE Record ontology.', $predicate), $predicate)]);
            }
            if (!$vocabulary->accepts($types, $predicate)) {
                throw new InvalidDocument(\sprintf('A logistics event does not accept %s.', $predicate), [\LambdaTwelve\OneRecord\Api\Error::of('Invalid resource', '400', \sprintf('A logistics event does not accept %s.', $predicate), $predicate)]);
            }
        }
        // Every value, not the first one: RDF values are unordered, and an event is for one object (R7-006).
        foreach ($event->graph->objects($event->iri, Cargo::eventFor) as $for) {
            if (!$for instanceof Iri) {
                throw new InvalidDocument('cargo:eventFor must reference a logistics object.', [\LambdaTwelve\OneRecord\Api\Error::of('Invalid resource', '400', 'cargo:eventFor must reference the logistics object the event was posted on.', Cargo::eventFor)]);
            }
            if (!$for->equals($object)) {
                throw new InvalidDocument('cargo:eventFor names another object.', [\LambdaTwelve\OneRecord\Api\Error::of('Invalid resource', '400', \sprintf('cargo:eventFor names %s but the event was posted on %s.', $for->value, $object->value), Cargo::eventFor)]);
            }
        }
    }
}
