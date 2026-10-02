<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use LambdaTwelve\OneRecord\Api\ServerInformation;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsObjectStore;

// Fixture for the layer rule: the client never touches the server, SPI included.
final class ServerReach
{
    public function fine(ServerInformation $information): ServerInformation
    {
        return $information;
    }

    public function wrong(LogisticsObjectStore $store): LogisticsObjectStore
    {
        return $store;
    }
}
