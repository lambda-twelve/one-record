<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use RuntimeException;

/**
 * An action request cannot move to the requested status from where it is
 * (revoking an applied change, accepting a rejected request, ...).
 */
final class IllegalTransition extends RuntimeException
{
    public function __construct(public readonly ActionRequest $request, public readonly RequestStatus $next)
    {
        parent::__construct(\sprintf('A %s in status %s cannot become %s.', $request->type->name, $request->status->shortName(), $next->shortName()));
    }
}
