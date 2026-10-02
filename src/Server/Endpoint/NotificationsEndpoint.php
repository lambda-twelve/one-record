<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Server\Event\NotificationReceived;
use LambdaTwelve\OneRecord\Server\Http\ContentNegotiation;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * POST /notifications: a partner tells us something happened on its server.
 * Any authenticated party may post; the server validates, raises an event
 * for the host and answers 204. What to do about it (fetch the object,
 * update a record) is the host's business.
 */
final class NotificationsEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        (new ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $notification = Notification::fromJsonLd($this->services->body->json($request));
        $this->services->logger->info('Notification received', ['from' => $agent->iri->value, 'eventType' => $notification->eventType->name, 'object' => $notification->logisticsObject?->value]);
        $this->services->dispatcher->dispatch(new NotificationReceived($notification, $agent));

        return $this->services->responder->empty(204, $negotiated);
    }
}
