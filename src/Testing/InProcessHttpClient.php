<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A PSR-18 client that hands every request to a PSR-15 handler in the same
 * process: the SDK client talking to a server without a socket. The bearer
 * token is passed on as the header HeaderAuthenticator trusts, so tests use
 * agent IRIs as tokens.
 */
final class InProcessHttpClient implements ClientInterface
{
    /** @var list<RequestInterface> */
    public array $requests = [];

    public function __construct(
        private readonly RequestHandlerInterface $handler,
        private readonly ServerRequestFactoryInterface $serverRequests,
        private readonly StreamFactoryInterface $streams,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        $serverRequest = $this->serverRequests->createServerRequest($request->getMethod(), $request->getUri())
            ->withProtocolVersion($request->getProtocolVersion())
            ->withBody($this->streams->createStream((string) $request->getBody()));
        foreach ($request->getHeaders() as $name => $values) {
            $serverRequest = $serverRequest->withHeader($name, $values);
        }
        if (preg_match('/^Bearer\s+(.+)$/', $request->getHeaderLine('Authorization'), $m) === 1) {
            $serverRequest = $serverRequest->withHeader(HeaderAuthenticator::HEADER, $m[1]);
        }

        return $this->handler->handle($serverRequest);
    }
}
