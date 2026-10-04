<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use PHPUnit\Framework\TestCase;

/**
 * What every ActionRequestStore must do. Extend it and return your store from
 * createStore(). Requests are saved whole and replaced whole: the store keeps
 * the latest state of each, never a history of its own.
 *
 * The tests live in ActionRequestStoreContractTests, a trait, for hosts that cannot extend this class.
 */
abstract class ActionRequestStoreContract extends TestCase
{
    use ActionRequestStoreContractTests;
}
