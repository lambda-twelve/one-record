<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use PHPUnit\Framework\TestCase;

/**
 * What every LogisticsObjectStore must do. Extend it, return your store from
 * createStore(), and the same tests that pass for the in-memory store prove
 * yours. Stores are expected to start empty.
 *
 * The tests live in LogisticsObjectStoreContractTests, a trait, for hosts that cannot extend this class.
 */
abstract class LogisticsObjectStoreContract extends TestCase
{
    use LogisticsObjectStoreContractTests;
}
