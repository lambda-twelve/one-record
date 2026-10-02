<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Support;

use Closure;
use LogicException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A PSR-18 client answering from a queue or a callable; records every request.
 */
final class FakeHttpClient implements ClientInterface
{
    /** @var list<RequestInterface> */
    public array $requests = [];

    /** @var list<ResponseInterface|Closure(RequestInterface): ResponseInterface|ClientExceptionInterface> */
    private array $queue = [];

    /**
     * @param ResponseInterface|Closure(RequestInterface): ResponseInterface|ClientExceptionInterface $response
     */
    public function queue(ResponseInterface|Closure|ClientExceptionInterface $response): self
    {
        $this->queue[] = $response;

        return $this;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        $next = array_shift($this->queue) ?? throw new LogicException('No response queued for ' . $request->getMethod() . ' ' . $request->getUri());
        if ($next instanceof ClientExceptionInterface) {
            throw $next;
        }

        return $next instanceof Closure ? $next($request) : $next;
    }
}
