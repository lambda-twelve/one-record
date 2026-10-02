<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server\InMemory;

use LambdaTwelve\OneRecord\Server\InMemory\InMemoryActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Testing\Contract\ActionRequestStoreContract;

final class InMemoryActionRequestStoreContractTest extends ActionRequestStoreContract
{
    protected function createStore(): ActionRequestStore
    {
        return new InMemoryActionRequestStore();
    }
}
