<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use PHPUnit\Framework\TestCase;

/**
 * What every LogisticsEventStore must do: an append-only log per object with
 * the spec's query filters. Extend it and return your store from createStore().
 *
 * The tests live in LogisticsEventStoreContractTests, a trait, for hosts that cannot extend this class.
 */
abstract class LogisticsEventStoreContract extends TestCase
{
    use LogisticsEventStoreContractTests;
}
