<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Event;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\RequestStatus;

final readonly class ActionRequestStatusChanged
{
    public function __construct(
        public ActionRequest $request,
        public RequestStatus $previous,
    ) {}
}
