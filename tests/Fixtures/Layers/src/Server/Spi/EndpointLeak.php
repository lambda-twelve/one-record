<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Server\Endpoint\SubscriptionsEndpoint;

// Fixture for the layer rule: the SPI may use documents, never endpoint code.
final class EndpointLeak
{
    public function fine(Subscription $subscription): Subscription
    {
        return $subscription;
    }

    public function wrong(SubscriptionsEndpoint $endpoint): SubscriptionsEndpoint
    {
        return $endpoint;
    }
}
