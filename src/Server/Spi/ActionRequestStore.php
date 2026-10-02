<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Action requests of every type. They are never deleted: revoked and
 * rejected ones stay readable and stay in the audit trail, as the spec requires.
 */
interface ActionRequestStore
{
    public function save(ActionRequest $request): void;

    public function get(Iri $iri): ?ActionRequest;

    /**
     * Change and verification requests about one object, oldest first: its audit trail.
     *
     * @return list<ActionRequest>
     */
    public function auditTrail(Iri $logisticsObject, AuditTrailQuery $query): array;

    /**
     * Pending change requests about one object, for rejecting the others
     * once one is accepted.
     *
     * @return list<ActionRequest>
     */
    public function pendingChanges(Iri $logisticsObject): array;

    /**
     * Every request of a type in the accepted state: the active subscriptions
     * and delegations. A SubscriptionStore built over this store derives its
     * subscribers from here, whatever either store is implemented with.
     *
     * @return list<ActionRequest>
     */
    public function accepted(ActionRequestType $type): array;
}
