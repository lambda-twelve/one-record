<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use PHPUnit\Framework\TestCase;

/**
 * What every NotificationOutbox must do: keep what the server enqueues, in
 * order, with the id, recipient and document intact, until the host delivers
 * it. The interface has only enqueue(); tell the contract how to look inside
 * your outbox with pending().
 *
 * The tests live in NotificationOutboxContractTests, a trait, for hosts that cannot extend this class.
 */
abstract class NotificationOutboxContract extends TestCase
{
    use NotificationOutboxContractTests;
}
