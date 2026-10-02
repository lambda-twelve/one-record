<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * One API operation. The server has already authenticated the caller and
 * negotiated the version when an endpoint runs; the endpoint applies access
 * control for its resource and throws HttpException for every failure.
 */
interface Endpoint
{
    /**
     * @param array<string, string> $parameters path parameters from the route
     */
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface;
}
