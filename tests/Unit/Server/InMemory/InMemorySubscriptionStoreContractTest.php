<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server\InMemory;

use LambdaTwelve\OneRecord\Server\InMemory\InMemoryActionRequestStore;
use LambdaTwelve\OneRecord\Server\InMemory\InMemorySubscriptionStore;
use LambdaTwelve\OneRecord\Testing\Contract\SubscriptionStoreContract;

final class InMemorySubscriptionStoreContractTest extends SubscriptionStoreContract
{
    protected function createStores(): array
    {
        $requests = new InMemoryActionRequestStore();

        return ['requests' => $requests, 'subscriptions' => new InMemorySubscriptionStore($requests)];
    }
}
