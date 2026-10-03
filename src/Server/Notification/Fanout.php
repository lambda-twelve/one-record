<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Notification;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\SubscriptionEventType;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Services;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;
use LambdaTwelve\OneRecord\Server\Spi\StoredObject;

/**
 * Decides who gets told about what, and enqueues the notifications. Subscribers
 * of an object (by URI or by type) hear about creation, updates and events
 * according to the event types they asked for; requesters who set
 * notifyRequestStatusChange hear about their action requests.
 */
final class Fanout
{
    public function __construct(private readonly Services $services) {}

    public function logisticsObjectCreated(StoredObject $stored, ?Iri $triggeredBy = null): void
    {
        $this->toSubscribers($stored, SubscriptionEventType::LogisticsObjectCreated, static fn(Notification $n): Notification => $n, $triggeredBy);
    }

    /**
     * @param list<string> $changedProperties
     */
    public function logisticsObjectUpdated(StoredObject $stored, array $changedProperties, ?Iri $triggeredBy = null): void
    {
        $this->toSubscribers($stored, SubscriptionEventType::LogisticsObjectUpdated, static fn(Notification $n): Notification => new Notification($n->eventType, $n->logisticsObject, $n->logisticsObjectType, $n->triggeredBy, $changedProperties, [], $n->body), $triggeredBy);
    }

    public function logisticsEventReceived(StoredObject $stored, LogisticsEvent $event): void
    {
        $this->toSubscribers($stored, SubscriptionEventType::LogisticsEventReceived, static fn(Notification $n): Notification => new Notification($n->eventType, $n->logisticsObject, $n->logisticsObjectType, $n->triggeredBy, [], [$event->iri], $n->body));
    }

    /**
     * Tells the requester about a status change when it asked to be told.
     */
    public function actionRequestStatusChanged(ActionRequest $request): void
    {
        if (!$request->notifyRequestStatusChange()) {
            return;
        }
        // api:hasLogisticsObject is at most one; a delegation over several objects names none of them
        // rather than an arbitrary first one, and the request in isTriggeredBy lists them all (spec question 32).
        $objects = $request->logisticsObjects();
        $object = \count($objects) === 1 ? $objects[0] : null;
        $type = null;
        if ($object !== null) {
            $stored = $this->services->objects->latest($object);
            $type = $stored?->object->mostSpecificType($this->services->vocabulary);
        }
        $notification = new Notification($request->type->notificationFor($request->status), $object, $type, $request->iri);
        $this->services->outbox->enqueue(new OutboundNotification($request->requestedBy, $notification, $this->services->clock->now(), $this->services->ids->next()));
    }

    /**
     * @param callable(Notification): Notification $decorate
     */
    private function toSubscribers(StoredObject $stored, SubscriptionEventType $eventType, callable $decorate, ?Iri $triggeredBy = null): void
    {
        $object = $stored->object;
        $type = $object->mostSpecificType($this->services->vocabulary);
        $now = $this->services->clock->now();
        foreach ($this->services->subscriptions->subscribersOf($object->iri, $object->types(), $now) as $entry) {
            $subscription = $entry['subscription'];
            if (!$subscription->includes($eventType)) {
                continue;
            }
            // A subscription says what to tell; the access policy says what may be disclosed (spec question 25).
            // No read permission: the subscriber learns that something happened, not what. Hidden: nothing at all.
            $decision = $this->services->policy->decide(new Agent($subscription->subscriber), Action::ReadLogisticsObject, $object->iri);
            if ($decision === Decision::Hide) {
                continue;
            }
            $base = new Notification(
                NotificationEventType::from($eventType->value),
                $object->iri,
                $type,
                $triggeredBy ?? $entry['request'],
                [],
                [],
                $subscription->sendLogisticsObjectBody && $decision === Decision::Allow ? $object : null,
            );
            $this->services->outbox->enqueue(new OutboundNotification($subscription->subscriber, $decorate($base), $now, $this->services->ids->next()));
        }
    }
}
