<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Model\LogisticsObject;

/**
 * The outcome of applying a Change: the new version of the object (the
 * caller assigns its revision) and the properties of the object itself that
 * changed, which notifications report in api:hasChangedProperty.
 */
final readonly class ChangeResult
{
    /**
     * @param list<string> $changedProperties property IRIs
     */
    public function __construct(
        public LogisticsObject $object,
        public array $changedProperties,
    ) {}
}
