<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A test double that trusts an X-Test-Agent header. Tests of endpoint
 * behaviour do not need real tokens; JwtAuthenticator has its own tests.
 *
 * It is an authentication bypass by design, so it defends against being wired
 * into a real deployment by mistake: outside the PHP CLI (PHPUnit) it
 * authenticates nobody unless constructed with $allowOutsideCli, which no
 * production configuration should ever pass.
 */
final class HeaderAuthenticator implements Authenticator
{
    public const string HEADER = 'X-Test-Agent';

    private readonly string $sapi;

    /**
     * @param bool $allowOutsideCli trust the header under a web SAPI too (an in-process test server, never production)
     * @param ?string $sapi the SAPI to decide on; defaults to PHP_SAPI, injectable for tests of this class
     */
    public function __construct(private readonly bool $allowOutsideCli = false, ?string $sapi = null)
    {
        $this->sapi = $sapi ?? PHP_SAPI;
    }

    public function authenticate(ServerRequestInterface $request): ?Agent
    {
        if (!$this->allowOutsideCli && !\in_array($this->sapi, ['cli', 'phpdbg'], true)) {
            return null;
        }
        $iri = $request->getHeaderLine(self::HEADER);

        return $iri === '' ? null : new Agent(new Iri($iri), 'https://test.issuer', ['sub' => $iri]);
    }
}
