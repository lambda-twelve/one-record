<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * GET /logistics-objects/{id}/logistics-events/{eventId}: one event. Access
 * follows GET_LOGISTICS_EVENT on the object.
 */
final class LogisticsEventEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $stored = $this->requireObject($parameters['id'], $agent, Action::ReadLogisticsEvent);
        $eventIri = $this->services->config->logisticsEventIri($parameters['id'], $parameters['eventId']);
        $event = $this->services->events->get($eventIri);
        if ($event === null || !$event->logisticsObject->equals($stored->object->iri)) {
            throw HttpException::notFound('Logistics Event', $eventIri->value);
        }
        $context = Context::oneRecord();
        $type = $this->services->vocabulary->mostSpecific($event->types())[0] ?? Cargo::LogisticsEvent;

        return $this->services->responder->jsonLd(200, $event->toJsonLd($context), $negotiated, $type, ['Location' => $eventIri->value, 'Last-Modified' => Responder::httpDate($event->created)], self::isHead($request));
    }
}
