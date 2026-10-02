<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server\InMemory;

use LambdaTwelve\OneRecord\Server\InMemory\InMemoryNotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Testing\Contract\NotificationOutboxContract;

final class InMemoryNotificationOutboxContractTest extends NotificationOutboxContract
{
    protected function createOutbox(): NotificationOutbox
    {
        return new InMemoryNotificationOutbox();
    }

    protected function pending(NotificationOutbox $outbox): array
    {
        self::assertInstanceOf(InMemoryNotificationOutbox::class, $outbox);

        return $outbox->all();
    }
}
