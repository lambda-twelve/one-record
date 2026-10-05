<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\ErrorDocument;
use LambdaTwelve\OneRecord\Api\InvalidDocument;
use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Server\Event\LogisticsEventReceived;
use LambdaTwelve\OneRecord\Server\Http\ContentNegotiation;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Notification\Fanout;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * POST /logistics-events (API 2.3, optional): one event for several objects,
 * each evaluated on its own, answered with 207 Multi-Status and an
 * api:MultiStatusResponse listing the outcome per object.
 */
final class BulkLogisticsEventsEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        (new ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $json = $this->services->body->json($request);
        // Expand once, so an alias for cargo:eventFor is understood like the single-object route does (AR-014).
        try {
            $expanded = JsonLd::expand($json);
        } catch (JsonLdException $e) {
            throw HttpException::badRequest($e->getMessage(), null, 'Invalid body request');
        }
        // Every target is judged before any is chosen: a malformed one is a client error, as on the
        // single-object route, not something to drop quietly from the multi-status answer (R14-003).
        $targetIris = [];
        foreach ($expanded->graph->objects($expanded->root, Cargo::eventFor) as $candidate) {
            if (!$candidate instanceof Iri) {
                throw HttpException::badRequest('cargo:eventFor must reference logistics objects by their URI.', Cargo::eventFor, 'Invalid resource');
            }
            $targetIris[$candidate->value] = $candidate;
        }
        if ($targetIris === []) {
            throw HttpException::badRequest('cargo:eventFor must list the logistics objects the event is for.', Cargo::eventFor, 'Invalid resource');
        }

        $now = $this->services->clock->now();
        $results = [];
        $created = 0;
        foreach ($targetIris as $target) {
            $relative = $this->services->config->relativePath($target);
            $objectId = $relative !== null && preg_match('#^logistics-objects/([^/]+)$#', $relative, $m) === 1 ? $m[1] : null;
            $decision = $objectId === null ? Decision::Hide : $this->services->policy->decide($agent, Action::PostLogisticsEvent, $target);
            $stored = $objectId !== null && $decision !== Decision::Hide ? $this->services->objects->latest($target) : null;
            if ($stored === null) {
                $results[] = $this->result(404, $target, null, Error::of('Resource not found', '404', 'Logistics Object could not be found.', null, $target->value), $negotiated->version);
                continue;
            }
            if ($decision !== Decision::Allow) {
                $results[] = $this->result(403, $target, null, Error::of('Not authorized to perform action', '403', 'The authenticated party is not authorized to post events on this object.', null, $target->value), $negotiated->version);
                continue;
            }
            $eventIri = $this->services->config->logisticsEventIri($objectId ?? '', $this->services->ids->next());
            $event = self::eventFor($expanded, $eventIri, $target, $now);
            try {
                if ($event->eventDate() === null) {
                    throw new InvalidDocument('Every logistics event must have a cargo:eventDate.', [Error::of('Invalid resource', '400', 'Every logistics event must have a cargo:eventDate.', Cargo::eventDate)]);
                }
                LogisticsEventsEndpoint::validateEvent($this->services->vocabulary, $event, $target);
            } catch (InvalidDocument $e) {
                $results[] = $this->result(400, $target, null, $e->errors[0] ?? Error::of('Invalid resource', '400', $e->getMessage()), $negotiated->version);
                continue;
            }
            $this->services->events->append($event);
            $this->services->dispatcher->dispatch(new LogisticsEventReceived($event, $agent));
            (new Fanout($this->services))->logisticsEventReceived($stored, $event);
            $results[] = $this->result(201, $target, $eventIri, null, $negotiated->version);
            $created++;
        }

        $document = [
            '@context' => Nodes::context(),
            '@id' => Namespaces::EMBEDDED . $this->services->ids->next(),
            '@type' => 'api:MultiStatusResponse',
            'api:hasTotalItems' => \count($results),
            'api:hasTotalCreated' => $created,
            'api:hasTotalFailed' => \count($results) - $created,
            'api:hasCreationResult' => $results,
        ];

        return $this->services->responder->jsonLd(207, $document, $negotiated, Api::MultiStatusResponse);
    }

    /**
     * The posted event as one object's event: the root renamed to the event's
     * URI, every eventFor replaced by this one target.
     */
    private static function eventFor(ExpandedDocument $expanded, Iri $eventIri, Iri $target, DateTimeImmutable $now): LogisticsEvent
    {
        $graph = new Graph();
        foreach ($expanded->graph as $triple) {
            if ($triple->predicate->value === Cargo::eventFor && $triple->subject->equals($expanded->root)) {
                continue;
            }
            $subject = $triple->subject->equals($expanded->root) ? $eventIri : $triple->subject;
            $object = $triple->object->equals($expanded->root) ? $eventIri : $triple->object;
            $graph->add(new Triple($subject, $triple->predicate, $object));
        }
        $graph->add(new Triple($eventIri, new Iri(Cargo::eventFor), $target));

        return new LogisticsEvent($eventIri, $target, $graph, $now);
    }

    /**
     * @return array<string, mixed>
     */
    private function result(int $status, Iri $object, ?Iri $event, ?Error $error, \LambdaTwelve\OneRecord\Spec\ApiVersion $version): array
    {
        $node = ['@id' => Namespaces::EMBEDDED . $this->services->ids->next(), '@type' => 'api:EventCreationResult', 'api:hasHTTPStatus' => $status, 'api:hasLogisticsObject' => Nodes::ref($object)];
        if ($event !== null) {
            $node['api:hasLogisticsEvent'] = Nodes::ref($event);
        }
        if ($error !== null) {
            $node['api:hasError'] = ErrorDocument::node($error, $version);
        }

        return $node;
    }
}
