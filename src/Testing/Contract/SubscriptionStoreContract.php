<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use PHPUnit\Framework\TestCase;

/**
 * What every SubscriptionStore must do, on both sides: the subscriptions this
 * host offers, and the subscribers derived from accepted subscription
 * requests in the ActionRequestStore it works with. Return both stores from
 * createStores(); they must see each other's writes.
 *
 * The tests live in SubscriptionStoreContractTests, a trait, for hosts that cannot extend this class.
 */
abstract class SubscriptionStoreContract extends TestCase
{
    use SubscriptionStoreContractTests;
}
