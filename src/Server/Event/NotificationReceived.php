<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Event;

use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Server\Spi\Agent;

/**
 * A partner told us about one of its objects or the status of a request we
 * made. The server stores nothing; the host reacts (fetch the object, update
 * its records).
 */
final readonly class NotificationReceived
{
    public function __construct(
        public Notification $notification,
        public Agent $sentBy,
    ) {}
}
