<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Event;

use LambdaTwelve\OneRecord\Api\ActionRequest;

/**
 * A partner asked for something: a change, a subscription, access, or a
 * verification. The host decides through DataHolder.
 */
final readonly class ActionRequestCreated
{
    public function __construct(public ActionRequest $request) {}
}
