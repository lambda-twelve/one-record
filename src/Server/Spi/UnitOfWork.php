<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

/**
 * The host's transaction boundary. Accepting a change saves a revision, saves
 * the request, rejects stale siblings and queues notifications: several store
 * calls that must stand or fall together. The server runs every mutating
 * request through `run()`, and DataHolder and ActionRequests run each of
 * their operations through it, so a host that binds its database transaction
 * here gets atomicity everywhere without inspecting status codes.
 *
 * Calls nest: an operation inside a request is already inside the request's
 * unit, and DataHolder::change() runs create and accept as one. An
 * implementation must therefore either join an open unit (savepoints, or a
 * depth counter) or tolerate nesting the way the identity implementation does.
 *
 * Exceptions thrown by `$work` propagate after the unit is rolled back; a
 * return value is committed and returned.
 */
interface UnitOfWork
{
    /**
     * @template T
     * @param callable(): T $work
     * @return T
     */
    public function run(callable $work): mixed;
}
