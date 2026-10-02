<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Model\LogisticsObject;

// Fixture for the layer rule: Change may use Api (declared pair) and Model. No error expected.
final class DeclaredPair
{
    public function fine(LogisticsObject $object, Error $error): array
    {
        return [$object, $error];
    }
}
