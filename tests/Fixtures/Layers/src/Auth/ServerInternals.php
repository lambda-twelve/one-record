<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth;

use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Server\Spi\Agent;

// Fixture for the layer rule: Auth may use the SPI, not the rest of the server.
final class ServerInternals
{
    public function fine(Agent $agent): Agent
    {
        return $agent;
    }

    public function wrong(ServerConfig $config): ServerConfig
    {
        return $config;
    }
}
