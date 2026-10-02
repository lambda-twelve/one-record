<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server\InMemory;

use LambdaTwelve\OneRecord\Server\InMemory\InMemoryAccessDelegationStore;
use LambdaTwelve\OneRecord\Server\Spi\AccessDelegationStore;
use LambdaTwelve\OneRecord\Testing\Contract\AccessDelegationStoreContract;

final class InMemoryAccessDelegationStoreContractTest extends AccessDelegationStoreContract
{
    protected function createStore(): AccessDelegationStore
    {
        return new InMemoryAccessDelegationStore();
    }
}
