<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\ErrorDocument;
use LambdaTwelve\OneRecord\Api\Nodes;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Rdf\Iri;
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
        $targets = $json['cargo:eventFor'] ?? $json[Cargo::eventFor] ?? null;
        $targetIris = [];
        foreach (\is_array($targets) ? (array_is_list($targets) ? $targets : [$targets]) : [] as $target) {
            $id = \is_array($target) ? ($target['@id'] ?? null) : $target;
            if (\is_string($id) && $id !== '') {
                try {
                    $targetIris[$id] = new Iri($id);
                } catch (InvalidArgumentException) {
                    throw HttpException::badRequest(\sprintf('"%s" is not a logistics object URI.', $id), Cargo::eventFor, 'Invalid resource');
                }
            }
        }
        if ($targetIris === []) {
            throw HttpException::badRequest('cargo:eventFor must list the logistics objects the event is for.', Cargo::eventFor, 'Invalid resource');
        }
        unset($json['cargo:eventFor'], $json[Cargo::eventFor]);

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
            $body = [...$json, 'cargo:eventFor' => Nodes::ref($target)];
            $event = LogisticsEvent::fromJsonLd($body, $eventIri, $target, $now);
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
