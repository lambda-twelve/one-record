<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth;

use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Signer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Serves the JWKS document for the keys this host signs with, so partners
 * can verify its tokens with JwksKeyResolver and key rotation needs no
 * out-of-band exchange. Mount it at /.well-known/jwks.json (or wherever the
 * issuer's metadata says). Several signers mean several keys: the current
 * one and the one being retired.
 */
final class JwksEndpoint implements RequestHandlerInterface
{
    /** @var list<Rs256Signer> */
    private readonly array $signers;

    /**
     * @param int $maxAgeSeconds how long verifiers may cache the document; keep it shorter than a rotation overlap
     */
    public function __construct(
        private readonly ResponseFactoryInterface $responses,
        private readonly StreamFactoryInterface $streams,
        private readonly int $maxAgeSeconds = 3600,
        Rs256Signer ...$signers,
    ) {
        $this->signers = array_values($signers);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!\in_array(strtoupper($request->getMethod()), ['GET', 'HEAD'], true)) {
            return $this->responses->createResponse(405)->withHeader('Allow', 'GET, HEAD');
        }
        $document = ['keys' => array_map(static fn(Rs256Signer $signer): array => $signer->publicJwk(), $this->signers)];
        $response = $this->responses->createResponse(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Cache-Control', 'public, max-age=' . $this->maxAgeSeconds);

        return strtoupper($request->getMethod()) === 'HEAD'
            ? $response
            : $response->withBody($this->streams->createStream(json_encode($document, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)));
    }
}
