<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\Verification;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\ChangeApplier;
use LambdaTwelve\OneRecord\Change\ChangeRejected;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Event\ActionRequestCreated;
use LambdaTwelve\OneRecord\Server\Event\ActionRequestStatusChanged;
use LambdaTwelve\OneRecord\Server\Event\LogisticsObjectRevised;
use LambdaTwelve\OneRecord\Server\Notification\Fanout;
use LambdaTwelve\OneRecord\Server\Spi\Grant;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;

/**
 * The action-request lifecycle the spec describes, shared by the HTTP
 * endpoints and the host's PHP API: creating requests, and moving them
 * through accept / reject / acknowledge / revoke with the consequences each
 * step has (applying a change, granting access, notifying).
 */
final class ActionRequests
{
    public function __construct(private readonly Services $services) {}

    public function create(Change|Subscription|AccessDelegation|Verification $payload, Iri $requestedBy): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($payload, $requestedBy): ActionRequest {
            $request = ActionRequest::create($this->services->config->actionRequestIri($this->services->ids->next()), $payload, $requestedBy, $this->services->clock->now());
            $this->services->actionRequests->save($request);
            $this->services->dispatcher->dispatch(new ActionRequestCreated($request));
            if ($request->notifyRequestStatusChange()) {
                (new Fanout($this->services))->actionRequestStatusChanged($request);
            }

            return $request;
        });
    }

    public function get(Iri $iri): ?ActionRequest
    {
        return $this->services->actionRequests->get($iri);
    }

    /**
     * Accept: a change is applied and becomes a new revision (or the request
     * fails with the errors recorded), a subscription becomes active, an
     * access delegation becomes grants. Other pending changes written against
     * the same revision are rejected, as the spec requires.
     */
    public function accept(ActionRequest $request, Iri $by): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request, $by): ActionRequest {
            $request = $this->current($request);
            $this->assertTransition($request, RequestStatus::Accepted);
            $now = $this->services->clock->now();
            $accepted = $request->withStatus(RequestStatus::Accepted, $now, $by);

            // The compare-and-set on the status is the decision, and it succeeds exactly once; every
            // side effect (grants, a revision, notifications) comes after it, so a decision that lost
            // the race writes nothing even under a host without a transactional unit of work. The
            // event and the status notification come last, once the consequences are in place, so a
            // listener reading the policy or the object sees the state after the decision.
            $previous = $request->status;
            $this->transition($accepted, $previous);
            $final = $accepted;
            if ($request->payload instanceof Change) {
                $final = $this->applyChange($accepted, $request->payload, $by);
            } elseif ($request->payload instanceof AccessDelegation) {
                foreach ($request->payload->delegates as $delegate) {
                    foreach ($request->payload->logisticsObjects as $object) {
                        $this->services->delegations->grant(new Grant($delegate, $object, $request->payload->permissions, $request->payload->expiresAt, $request->iri));
                        $stored = $this->services->objects->latest($object);
                        $this->services->outbox->enqueue(new OutboundNotification($delegate, new Notification(NotificationEventType::LogisticsObjectAccessGranted, $object, $stored?->object->mostSpecificType($this->services->vocabulary), $request->iri), $now, $this->services->ids->next()));
                    }
                }
            }
            $this->announce($final, $previous);

            return $final;
        });
    }

    /**
     * @param list<Error> $errors
     */
    public function reject(ActionRequest $request, Iri $by, array $errors = []): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request, $by, $errors): ActionRequest {
            $request = $this->current($request);
            $this->assertTransition($request, RequestStatus::Rejected);
            $rejected = $request->withStatus(RequestStatus::Rejected, $this->services->clock->now(), $by, $errors);
            $this->transition($rejected, $request->status);
            $this->announce($rejected, $request->status);

            return $rejected;
        });
    }

    public function acknowledge(ActionRequest $request, Iri $by): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request, $by): ActionRequest {
            $request = $this->current($request);
            $this->assertTransition($request, RequestStatus::Acknowledged);
            $acknowledged = $request->withStatus(RequestStatus::Acknowledged, $this->services->clock->now(), $by);
            $this->transition($acknowledged, $request->status);
            $this->announce($acknowledged, $request->status);

            return $acknowledged;
        });
    }

    public function revoke(ActionRequest $request, Iri $by): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request, $by): ActionRequest {
            $request = $this->current($request);
            $this->assertTransition($request, RequestStatus::Revoked);
            $revoked = $request->withStatus(RequestStatus::Revoked, $this->services->clock->now(), $by);
            $this->transition($revoked, $request->status);
            if ($request->type === ActionRequestType::AccessDelegation && $request->status === RequestStatus::Accepted) {
                $this->services->delegations->revokeFrom($request->iri);
            }
            $this->announce($revoked, $request->status);

            return $revoked;
        });
    }

    /**
     * @param list<Error> $errors
     */
    public function fail(ActionRequest $request, array $errors): ActionRequest
    {
        return $this->services->unitOfWork->run(function () use ($request, $errors): ActionRequest {
            $request = $this->current($request);
            $this->assertTransition($request, RequestStatus::Failed);
            $failed = $request->withStatus(RequestStatus::Failed, $this->services->clock->now(), null, $errors);
            $this->transition($failed, $request->status);
            $this->announce($failed, $request->status);

            return $failed;
        });
    }

    /**
     * Called once the request is stored as Accepted: applying the change may
     * still fail, which moves the request on to Failed.
     */
    private function applyChange(ActionRequest $accepted, Change $change, Iri $by): ActionRequest
    {
        $current = $this->services->objects->latest($change->logisticsObject);
        if ($current === null) {
            $failed = $accepted->withStatus(RequestStatus::Failed, $this->services->clock->now(), null, [Error::of('Resource not found', '404', 'The logistics object no longer exists.', null, $change->logisticsObject->value)]);
            $this->transition($failed, RequestStatus::Accepted);

            return $failed;
        }
        $applier = new ChangeApplier($this->services->vocabulary, $this->services->embeddedIds);
        try {
            $result = $applier->apply($current->object, $current->revision, $change);
            $stored = $this->services->objects->saveRevision($result->object, $current->revision, $this->services->clock->now());
        } catch (ChangeRejected $e) {
            $failed = $accepted->withStatus(RequestStatus::Failed, $this->services->clock->now(), null, $e->errors);
            $this->transition($failed, RequestStatus::Accepted);

            return $failed;
        } catch (StoreException $e) {
            $failed = $accepted->withStatus(RequestStatus::Failed, $this->services->clock->now(), null, [Error::of('Conflict with Logistics Object revision number', '409', $e->getMessage(), null, $change->logisticsObject->value)]);
            $this->transition($failed, RequestStatus::Accepted);

            return $failed;
        }

        $this->services->dispatcher->dispatch(new LogisticsObjectRevised($stored, $accepted->iri, $result->changedProperties));
        (new Fanout($this->services))->logisticsObjectUpdated($stored, $result->changedProperties, $accepted->iri);

        // Every other pending change written against the revision just replaced is now stale.
        foreach ($this->services->actionRequests->pendingChanges($change->logisticsObject) as $other) {
            if (!$other->iri->equals($accepted->iri) && $other->payload instanceof Change && $other->payload->revision === $change->revision) {
                $this->reject($other, $by, [Error::of('Conflict with Logistics Object revision number', '409', \sprintf('Another change to revision %d was accepted first; the object is now at revision %d.', $change->revision, $stored->revision), null, $change->logisticsObject->value)]);
            }
        }

        return $accepted;
    }

    private function assertTransition(ActionRequest $request, RequestStatus $next): void
    {
        if (!$request->canTransitionTo($next)) {
            throw new IllegalTransition($request, $next);
        }
    }

    /**
     * The stored state of a request, whatever snapshot the caller holds: a
     * decision made on a stale copy would otherwise undo a newer one (AR-002).
     */
    private function current(ActionRequest $request): ActionRequest
    {
        return $this->services->actionRequests->get($request->iri) ?? throw StoreException::notFound($request->iri);
    }

    /**
     * The compare-and-set that is the decision: it succeeds for exactly one worker.
     */
    private function transition(ActionRequest $request, RequestStatus $previous): void
    {
        $this->services->actionRequests->transition($request, $previous);
    }

    /**
     * Tells the host and the requester about a decision whose side effects are
     * already in place; $previous is the status the request had before the
     * decision, whatever intermediate state the store recorded on the way.
     */
    private function announce(ActionRequest $request, RequestStatus $previous): void
    {
        $this->services->dispatcher->dispatch(new ActionRequestStatusChanged($request, $previous));
        (new Fanout($this->services))->actionRequestStatusChanged($request);
    }
}
