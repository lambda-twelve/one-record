<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\Error;
use RuntimeException;

/**
 * The holder's own change was accepted and could not be applied; no revision
 * was written. Thrown from DataHolder::change(), update() and publish() so the
 * caller, and its unit of work, see a failure instead of a request object to
 * inspect. The request carried here is diagnostic: it was saved as
 * REQUEST_FAILED inside the unit of work, so a transactional host rolls it
 * back with everything else, while the identity unit of work keeps it.
 */
final class ChangeFailed extends RuntimeException
{
    public function __construct(public readonly ActionRequest $request)
    {
        $reasons = implode('; ', array_map(static fn(Error $e): string => $e->title . (($e->details[0]->message ?? null) !== null ? ' (' . $e->details[0]->message . ')' : ''), $request->errors));
        parent::__construct(\sprintf(
            'The change to %s was not applied (%s): %s',
            $request->logisticsObjects()[0]->value ?? 'the object',
            $request->status->shortName(),
            $reasons === '' ? 'no details recorded' : $reasons,
        ));
    }
}
