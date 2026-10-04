<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing;

use Closure;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\AuditTrailQuery;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use LambdaTwelve\OneRecord\Server\Spi\Volatile;

/**
 * Stages the race every host must prove it survives: another worker decides
 * the same action request first. Decorates any ActionRequestStore; arm it with
 * a request and the next transition() of that request either throws the
 * status conflict outright, or first runs your callback (flip your own row
 * to the competing status there) and then lets the real compare-and-set lose
 * on its own. Fires once, then behaves like the store it wraps.
 */
final class RacingActionRequestStore implements ActionRequestStore, Volatile
{
    private ?Iri $armed = null;

    /** @var ?Closure(ActionRequest): void */
    private ?Closure $before = null;

    public int $racesLost = 0;

    public function __construct(private readonly ActionRequestStore $inner) {}

    /**
     * @param ?callable(ActionRequest): void $before what the competing worker did; null throws the conflict directly
     */
    public function arm(Iri $request, ?callable $before = null): void
    {
        $this->armed = $request;
        $this->before = $before === null ? null : $before(...);
    }

    public function transition(ActionRequest $request, RequestStatus $expectedCurrent): void
    {
        if ($this->armed !== null && $this->armed->equals($request->iri)) {
            $before = $this->before;
            $this->armed = null;
            $this->before = null;
            $this->racesLost++;
            if ($before === null) {
                throw StoreException::statusConflict($request->iri, $expectedCurrent->shortName(), 'another worker decided first');
            }
            $before($request);
        }
        $this->inner->transition($request, $expectedCurrent);
    }

    public function save(ActionRequest $request): void
    {
        $this->inner->save($request);
    }

    public function get(Iri $iri): ?ActionRequest
    {
        return $this->inner->get($iri);
    }

    public function auditTrail(Iri $logisticsObject, AuditTrailQuery $query): array
    {
        return $this->inner->auditTrail($logisticsObject, $query);
    }

    public function pendingChanges(Iri $logisticsObject): array
    {
        return $this->inner->pendingChanges($logisticsObject);
    }

    public function accepted(ActionRequestType $type): array
    {
        return $this->inner->accepted($type);
    }
}
