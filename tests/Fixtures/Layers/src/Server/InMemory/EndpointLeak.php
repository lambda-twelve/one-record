<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Server\Endpoint\LogisticsObjectEndpoint;
use LambdaTwelve\OneRecord\Server\Services;

// Fixture for the layer rule: in-memory wiring may use Services, never an endpoint.
final class EndpointLeak
{
    public function fine(Services $services): Services
    {
        return $services;
    }

    public function wrong(LogisticsObjectEndpoint $endpoint): LogisticsObjectEndpoint
    {
        return $endpoint;
    }
}
