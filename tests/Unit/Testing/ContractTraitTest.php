<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Testing;

use LambdaTwelve\OneRecord\Server\InMemory\InMemoryNotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Testing\Contract\NotificationOutboxContractTests;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * A host whose test case extends a framework base class uses the contract as
 * a trait; this proves the trait runs on a plain TestCase exactly as the
 * abstract contract does.
 */
#[CoversNothing]
final class ContractTraitTest extends TestCase
{
    use NotificationOutboxContractTests;

    protected function createOutbox(): NotificationOutbox
    {
        return new InMemoryNotificationOutbox();
    }

    protected function pending(NotificationOutbox $outbox): array
    {
        \assert($outbox instanceof InMemoryNotificationOutbox);

        return $outbox->all();
    }
}
