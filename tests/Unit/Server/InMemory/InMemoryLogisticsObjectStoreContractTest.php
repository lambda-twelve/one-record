<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server\InMemory;

use LambdaTwelve\OneRecord\Server\InMemory\InMemoryLogisticsObjectStore;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsObjectStore;
use LambdaTwelve\OneRecord\Testing\Contract\LogisticsObjectStoreContract;

final class InMemoryLogisticsObjectStoreContractTest extends LogisticsObjectStoreContract
{
    protected function createStore(): LogisticsObjectStore
    {
        return new InMemoryLogisticsObjectStore();
    }
}
