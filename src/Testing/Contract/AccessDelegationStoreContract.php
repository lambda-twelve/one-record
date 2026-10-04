<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use PHPUnit\Framework\TestCase;

/**
 * What every AccessDelegationStore must do. Extend it and return your store
 * from createStore().
 *
 * The tests live in AccessDelegationStoreContractTests, a trait, for hosts that cannot extend this class.
 */
abstract class AccessDelegationStoreContract extends TestCase
{
    use AccessDelegationStoreContractTests;
}
