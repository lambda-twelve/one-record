<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Event;

use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Server\Spi\Agent;

/**
 * A partner posted a logistics event (a status update) on one of this
 * server's objects. Hosts turn these into whatever their business needs.
 */
final readonly class LogisticsEventReceived
{
    public function __construct(
        public LogisticsEvent $event,
        public Agent $postedBy,
    ) {}
}
