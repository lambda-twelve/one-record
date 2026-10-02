<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Model\IriMinter;
use LambdaTwelve\OneRecord\Model\LocalGraph;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\ResolvedGraph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Event\LogisticsObjectCreated;
use LambdaTwelve\OneRecord\Server\Notification\Fanout;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;
use LambdaTwelve\OneRecord\Server\Spi\StoredObject;

/**
 * The holder's side of the API, in PHP: publish objects, decide action
 * requests, share and forget. This is what a host calls from its own code;
 * partners reach the same state through the HTTP endpoints.
 *
 * Changes the holder makes to its own objects are recorded as accepted
 * change requests in the audit trail, as the spec asks for a complete
 * history whatever the source of a change.
 */
final class DataHolder
{
    private readonly ActionRequests $requests;

    public function __construct(private readonly Services $services)
    {
        $this->requests = new ActionRequests($services);
    }

    /**
     * Create an object that does not exist yet.
     */
    public function create(LogisticsObject $object): StoredObject
    {
        return $this->services->unitOfWork->run(function () use ($object): StoredObject {
            $stored = $this->services->objects->create($object->withEmbeddedIds($this->services->embeddedIds), $this->services->clock->now());
            $this->services->dispatcher->dispatch(new LogisticsObjectCreated($stored, $this->services->config->dataHolder));
            (new Fanout($this->services))->logisticsObjectCreated($stored);

            return $stored;
        });
    }

    /**
     * Bring a stored object to this version: computed as a change, recorded as
     * an accepted change request, applied as a new revision. Null when nothing
     * differs.
     */
    public function update(LogisticsObject $object, ?string $description = null): ?ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($object, $description): ?ActionRequest {
            $current = $this->services->objects->latest($object->iri)
                ?? throw new InvalidArgumentException(\sprintf('"%s" does not exist; create it first.', $object->iri->value));
            $change = (new ChangeBuilder($this->services->vocabulary))->diff($current->object, $object, $current->revision, $description);
            if ($change === null) {
                return null;
            }

            return $this->change($change);
        });
    }

    /**
     * Apply a change of the holder's own making: created and accepted at once.
     */
    public function change(Change $change): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($change): ActionRequest {
            $request = $this->requests->create($change, $this->services->config->dataHolder);

            return $this->requests->accept($request, $this->services->config->dataHolder);
        });
    }

    /**
     * Create or update every object of a graph, idempotently: unchanged objects
     * produce nothing, changed ones a new revision. Pass the URIs of objects
     * already published so they keep them.
     *
     * @param array<string, Iri> $existing local key => URI
     */
    public function publish(LocalGraph|ResolvedGraph $graph, ?IriMinter $minter = null, array $existing = []): PublishResult
    {
        return $this->services->unitOfWork->run(function () use ($graph, $minter, $existing): PublishResult {
            if ($graph instanceof LocalGraph) {
                $graph = $graph->resolve($minter ?? throw new InvalidArgumentException('A LocalGraph needs an IriMinter to publish.'), $existing);
            }
            $outcomes = [];
            foreach ($graph->objects as $key => $object) {
                if (!$this->services->objects->exists($object->iri)) {
                    $this->create($object);
                    $outcomes[$key] = PublishResult::CREATED;
                    continue;
                }
                $outcomes[$key] = $this->update($object, 'Republished by the data holder') === null ? PublishResult::UNCHANGED : PublishResult::UPDATED;
            }

            return new PublishResult($graph, $outcomes);
        });
    }

    public function accept(Iri $request): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request): ActionRequest {
            return $this->requests->accept($this->require($request), $this->services->config->dataHolder);
        });
    }

    /**
     * @param list<Error> $errors
     */
    public function reject(Iri $request, array $errors = []): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request, $errors): ActionRequest {
            return $this->requests->reject($this->require($request), $this->services->config->dataHolder, $errors);
        });
    }

    public function acknowledge(Iri $request): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request): ActionRequest {
            return $this->requests->acknowledge($this->require($request), $this->services->config->dataHolder);
        });
    }

    public function revoke(Iri $request): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request): ActionRequest {
            return $this->requests->revoke($this->require($request), $this->services->config->dataHolder);
        });
    }

    /**
     * Publisher-initiated subscription (spec: "Get Subscription information as
     * Publisher"): the subscription a partner answered with becomes an
     * accepted SubscriptionRequest, so notifications can reference it and the
     * partner can revoke it.
     */
    public function subscribe(Subscription $subscription): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($subscription): ActionRequest {
            $request = $this->requests->create($subscription, $subscription->subscriber);

            return $this->requests->accept($request, $this->services->config->dataHolder);
        });
    }

    /**
     * Tell a partner an object exists (LOGISTICS_OBJECT_AVAILABLE): the spec's
     * way of sharing a URI so the partner can read or subscribe to it.
     */
    public function announce(Iri $object, Iri $recipient): void
    {
        $this->services->unitOfWork->run(function () use ($object, $recipient): void {
            $stored = $this->services->objects->latest($object) ?? throw new InvalidArgumentException(\sprintf('"%s" does not exist.', $object->value));
            $notification = new Notification(NotificationEventType::LogisticsObjectAvailable, $object, $stored->object->mostSpecificType($this->services->vocabulary));
            $this->services->outbox->enqueue(new OutboundNotification($recipient, $notification, $this->services->clock->now(), $this->services->ids->next()));
        });
    }

    /**
     * Forget an object: every revision is erased, and by default its events
     * and the grants on it too. The API has no delete, so this is the host's
     * data-protection operation. Keep events (false) where status history
     * must outlive the object; action requests are never erased, they are
     * other parties' history too. The host's own access rules must stop
     * answering for the URI as well.
     */
    public function forget(Iri $object, bool $events = true, bool $grants = true): void
    {
        $this->services->unitOfWork->run(function () use ($object, $events, $grants): void {
            $this->services->objects->erase($object);
            if ($events) {
                $this->services->events->eraseFor($object);
            }
            if ($grants) {
                $this->services->delegations->eraseFor($object);
            }
        });
    }

    public function actionRequests(): ActionRequests
    {
        return $this->requests;
    }

    private function require(Iri $iri): ActionRequest
    {
        return $this->requests->get($iri) ?? throw new InvalidArgumentException(\sprintf('No action request "%s".', $iri->value));
    }
}
