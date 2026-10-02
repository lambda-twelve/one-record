<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server\InMemory;

use LambdaTwelve\OneRecord\Server\InMemory\InMemoryLogisticsEventStore;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsEventStore;
use LambdaTwelve\OneRecord\Testing\Contract\LogisticsEventStoreContract;

final class InMemoryLogisticsEventStoreContractTest extends LogisticsEventStoreContract
{
    protected function createStore(): LogisticsEventStore
    {
        return new InMemoryLogisticsEventStore();
    }
}
