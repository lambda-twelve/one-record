<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\Error;
use RuntimeException;

/**
 * The holder's own change was accepted and could not be applied: the request
 * is recorded as REQUEST_FAILED with these errors, no revision was written.
 * Thrown from DataHolder::change(), update() and publish() so the caller, and
 * its unit of work, see a failure instead of a request object to inspect.
 */
final class ChangeFailed extends RuntimeException
{
    public function __construct(public readonly ActionRequest $request)
    {
        $reasons = implode('; ', array_map(static fn(Error $e): string => $e->title . (($e->details[0]->message ?? null) !== null ? ' (' . $e->details[0]->message . ')' : ''), $request->errors));
        parent::__construct(\sprintf(
            'The change to %s failed: %s',
            $request->logisticsObjects()[0]->value ?? 'the object',
            $reasons === '' ? 'no details recorded' : $reasons,
        ));
    }
}
