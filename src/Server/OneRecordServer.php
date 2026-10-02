<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Api\InvalidDocument;
use LambdaTwelve\OneRecord\Server\Http\ContentNegotiation;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

/**
 * The ONE Record server as one PSR-15 request handler: mount it under any
 * path and every endpoint of the API is served below it.
 *
 * Request pipeline: negotiate the API version and language, authenticate,
 * route, run the endpoint. Every failure becomes an api:Error body with the
 * spec's status code. Nothing here knows about storage, frameworks or
 * business rules; those arrive through the SPI the endpoints are built with.
 */
final class OneRecordServer implements RequestHandlerInterface
{
    public function __construct(
        private readonly ServerConfig $config,
        private readonly Router $router,
        private readonly Authenticator $authenticator,
        private readonly Responder $responder,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $negotiation = new ContentNegotiation($this->config);
        $head = strtoupper($request->getMethod()) === 'HEAD';
        try {
            $negotiated = $negotiation->negotiate($request);
        } catch (HttpException $e) {
            // Without an acceptable format we still answer JSON-LD in our highest version: there is nothing else to answer in.
            return $this->responder->error($e->status, $e->errors, new Negotiated($this->config->highestApiVersion(), 'en-US', false), $e->headers, $head);
        }

        try {
            $path = $this->path($request);
            $match = $this->router->match($request->getMethod(), $path);
            if ($match === null) {
                throw HttpException::notFound('The requested resource', $this->config->endpoint() . $path);
            }
            if (isset($match['allowed'])) {
                throw HttpException::methodNotAllowed($match['allowed']);
            }
            if (!$negotiated->version->isAtLeast($match['since'])) {
                throw HttpException::notFound(\sprintf('This endpoint exists from API %s; the request negotiated %s. The resource', $match['since']->value, $negotiated->version->value), $this->config->endpoint() . $path);
            }

            $agent = $this->authenticator->authenticate($request);
            if ($agent === null) {
                throw HttpException::unauthenticated();
            }

            return $match['endpoint']->handle($request, $agent, $negotiated, $match['parameters']);
        } catch (HttpException $e) {
            return $this->responder->error($e->status, $e->errors, $negotiated, $e->headers, $head);
        } catch (InvalidDocument $e) {
            return $this->responder->error(400, $e->errors, $negotiated, [], $head);
        } catch (Throwable $e) {
            $this->logger->error('Unhandled error while serving a ONE Record request', ['exception' => $e, 'method' => $request->getMethod(), 'path' => $request->getUri()->getPath()]);
            $internal = HttpException::internal();

            return $this->responder->error($internal->status, $internal->errors, $negotiated, [], $head);
        }
    }

    /**
     * The request path relative to the base path, always starting with "/".
     */
    private function path(ServerRequestInterface $request): string
    {
        $path = $request->getUri()->getPath();
        $base = $this->config->basePath;
        if ($base !== '') {
            // Nothing exists outside the base path the host mounted us under.
            if ($path !== $base && !str_starts_with($path, $base . '/')) {
                throw HttpException::notFound('The requested resource', $this->config->baseUrl . $path);
            }
            $path = substr($path, \strlen($base));
        }
        $path = '/' . ltrim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }

    public function config(): ServerConfig
    {
        return $this->config;
    }

    /** @internal */
    public static function versionLabel(ApiVersion $version): string
    {
        return $version->value;
    }
}
