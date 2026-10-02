<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\AuditTrailQuery;

final class InMemoryActionRequestStore implements ActionRequestStore
{
    /** @var array<string, ActionRequest> */
    private array $requests = [];

    public function save(ActionRequest $request): void
    {
        $this->requests[$request->iri->value] = $request;
    }

    public function get(Iri $iri): ?ActionRequest
    {
        return $this->requests[$iri->value] ?? null;
    }

    public function auditTrail(Iri $logisticsObject, AuditTrailQuery $query): array
    {
        $out = [];
        foreach ($this->requests as $request) {
            if (!\in_array($request->type, [ActionRequestType::Change, ActionRequestType::Verification], true)) {
                continue;
            }
            if (array_filter($request->logisticsObjects(), static fn(Iri $i): bool => $i->equals($logisticsObject)) === []) {
                continue;
            }
            $modified = $request->lastModified();
            if ($query->updatedFrom !== null && $modified < $query->updatedFrom) {
                continue;
            }
            if ($query->updatedTo !== null && $modified > $query->updatedTo) {
                continue;
            }
            if ($query->status !== null && $request->status !== $query->status) {
                continue;
            }
            $out[] = $request;
        }
        usort($out, static function (ActionRequest $a, ActionRequest $b): int {
            $byTime = $a->requestedAt <=> $b->requestedAt;

            return $byTime !== 0 ? $byTime : strcmp($a->iri->value, $b->iri->value);
        });

        return $out;
    }

    public function pendingChanges(Iri $logisticsObject): array
    {
        return array_values(array_filter(
            $this->auditTrail($logisticsObject, new AuditTrailQuery(status: RequestStatus::Pending)),
            static fn(ActionRequest $r): bool => $r->type === ActionRequestType::Change,
        ));
    }

    public function accepted(ActionRequestType $type): array
    {
        return array_values(array_filter($this->requests, static fn(ActionRequest $r): bool => $r->type === $type && $r->status === RequestStatus::Accepted));
    }

    /**
     * @return list<ActionRequest>
     */
    public function all(): array
    {
        return array_values($this->requests);
    }
}
