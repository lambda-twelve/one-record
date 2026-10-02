<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Server\Spi\UnitOfWork;

/**
 * No transaction at all: the work runs, nested or not. The default when a host
 * gives no UnitOfWork, right for the in-memory stores, which have nothing to
 * roll back.
 */
final class IdentityUnitOfWork implements UnitOfWork
{
    public function run(callable $work): mixed
    {
        return $work();
    }
}
