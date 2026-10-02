<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Support;

use Nyholm\Psr7\ServerRequest;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A PSR-18 client that hands every request to a PSR-15 handler in the same
 * process: the SDK client talking to the SDK server without a socket.
 * The bearer token is passed on as the X-Test-Agent header the
 * HeaderAuthenticator trusts, so tests use agent IRIs as tokens.
 */
final class InProcessHttpClient implements ClientInterface
{
    /** @var list<RequestInterface> */
    public array $requests = [];

    public function __construct(private readonly RequestHandlerInterface $handler) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        $headers = $request->getHeaders();
        if (preg_match('/^Bearer\s+(.+)$/', $request->getHeaderLine('Authorization'), $m) === 1) {
            $headers['X-Test-Agent'] = [$m[1]];
        }
        $serverRequest = new ServerRequest($request->getMethod(), $request->getUri(), $headers, (string) $request->getBody(), $request->getProtocolVersion());

        return $this->handler->handle($serverRequest);
    }
}
