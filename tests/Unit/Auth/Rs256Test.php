<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Auth;

use DateTimeImmutable;
use InvalidArgumentException;
use LambdaTwelve\OneRecord\Auth\Jwt\Claims;
use LambdaTwelve\OneRecord\Auth\Jwt\Jwk;
use LambdaTwelve\OneRecord\Auth\Jwt\JwksKeyResolver;
use LambdaTwelve\OneRecord\Auth\Jwt\JwtException;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Signer;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Verifier;
use LambdaTwelve\OneRecord\Auth\Jwt\StaticKeyResolver;
use LambdaTwelve\OneRecord\Tests\Support\ArrayCache;
use LambdaTwelve\OneRecord\Tests\Support\FakeHttpClient;
use LambdaTwelve\OneRecord\Tests\Support\FixedClock;
use LambdaTwelve\OneRecord\Tests\Support\TestKeys;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(Rs256Signer::class)]
#[CoversClass(Rs256Verifier::class)]
#[CoversClass(Claims::class)]
#[CoversClass(Jwk::class)]
#[CoversClass(JwtException::class)]
#[CoversClass(StaticKeyResolver::class)]
#[CoversClass(JwksKeyResolver::class)]
final class Rs256Test extends TestCase
{
    private const string ISSUER = 'https://auth.example.com';
    private const string AGENT = 'https://1r.example.com/logistics-objects/957e2622-9d31-493b-8b8f-3c805064dbda';

    private function signer(string $keyName = 'default', ?string $kid = null, ?FixedClock $clock = null): Rs256Signer
    {
        return new Rs256Signer(TestKeys::pair($keyName)['private'], self::ISSUER, $clock ?? new FixedClock(), $kid, static fn(int $n): string => str_repeat("\x42", $n));
    }

    private function verifier(?FixedClock $clock = null, ?string $audience = null, string $keyName = 'default'): Rs256Verifier
    {
        return new Rs256Verifier(new StaticKeyResolver([self::ISSUER => TestKeys::pair($keyName)['public']]), $clock ?? new FixedClock(), $audience);
    }

    public function testSignsAndVerifiesATokenWithTheOneRecordClaims(): void
    {
        $clock = new FixedClock('2026-10-02T12:00:00Z');
        $token = $this->signer(clock: $clock)->sign(['sub' => 'partner-lh', 'aud' => 'one-record', Claims::LOGISTICS_AGENT_URI => self::AGENT], 300);

        self::assertCount(3, explode('.', $token));
        self::assertSame('{"alg":"RS256","typ":"JWT"}', Jwk::base64UrlDecode(explode('.', $token)[0]));

        $claims = $this->verifier($clock, 'one-record')->verify($token);
        self::assertSame(self::ISSUER, $claims->issuer());
        self::assertSame('partner-lh', $claims->subject());
        self::assertSame(['one-record'], $claims->audience());
        self::assertSame(self::AGENT, $claims->logisticsAgentUri());
        self::assertSame($clock->now()->getTimestamp(), $claims->issuedAt());
        self::assertSame($clock->now()->getTimestamp(), $claims->notBefore());
        self::assertSame($clock->now()->getTimestamp() + 300, $claims->expiresAt());
        self::assertSame(str_repeat('42', 16), $claims->tokenId());
        self::assertSame('partner-lh', $claims->get('sub'));
    }

    public function testCallerCannotOverrideIssuerOrTimes(): void
    {
        $token = $this->signer()->sign(['iss' => 'https://evil.example', 'exp' => 1, 'nbf' => 0], 60);
        $claims = $this->verifier()->verify($token);

        self::assertSame(self::ISSUER, $claims->issuer());
        self::assertSame((new FixedClock())->now()->getTimestamp() + 60, $claims->expiresAt());
    }

    /**
     * @return iterable<string, array{callable(Rs256Test): string, string}>
     */
    public static function rejectedTokens(): iterable
    {
        $forge = static function (array $header, array $payload, string $signature = 'c2ln'): string {
            return Jwk::base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR)) . '.' . Jwk::base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR)) . '.' . $signature;
        };
        $payload = ['iss' => self::ISSUER, 'exp' => (new FixedClock())->now()->getTimestamp() + 60, Claims::LOGISTICS_AGENT_URI => self::AGENT];

        yield 'two parts' => [static fn(): string => 'a.b', JwtException::MALFORMED];
        yield 'empty signature' => [static fn(): string => $forge(['alg' => 'RS256'], $payload, ''), JwtException::MALFORMED];
        yield 'header not json' => [static fn(): string => 'bm90anNvbg.' . Jwk::base64UrlEncode('{}') . '.c2ln', JwtException::MALFORMED];
        yield 'header is a list' => [static fn(): string => Jwk::base64UrlEncode('[1]') . '.' . Jwk::base64UrlEncode('{}') . '.c2ln', JwtException::MALFORMED];
        yield 'bad base64' => [static fn(): string => '!!!.' . Jwk::base64UrlEncode('{}') . '.c2ln', JwtException::MALFORMED];
        yield 'alg none' => [static fn(): string => $forge(['alg' => 'none'], $payload), JwtException::UNSUPPORTED_ALGORITHM];
        yield 'alg HS256' => [static function () use ($payload): string {
            $signed = Jwk::base64UrlEncode('{"alg":"HS256","typ":"JWT"}') . '.' . Jwk::base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
            // Signed with the public key as the HMAC secret: the classic algorithm confusion attack.
            return $signed . '.' . Jwk::base64UrlEncode(hash_hmac('sha256', $signed, TestKeys::pair()['public'], true));
        }, JwtException::UNSUPPORTED_ALGORITHM];
        yield 'alg RS512' => [static fn(): string => $forge(['alg' => 'RS512'], $payload), JwtException::UNSUPPORTED_ALGORITHM];
        yield 'unsupported typ' => [static fn(): string => $forge(['alg' => 'RS256', 'typ' => 'JWE'], $payload), JwtException::UNSUPPORTED_ALGORITHM];
        yield 'crit header' => [static fn(): string => $forge(['alg' => 'RS256', 'crit' => ['b64'], 'b64' => false], $payload), JwtException::UNSUPPORTED_ALGORITHM];
        yield 'missing issuer' => [static fn(): string => $forge(['alg' => 'RS256'], ['exp' => 1]), JwtException::MISSING_CLAIM];
        yield 'unknown issuer' => [static fn(): string => $forge(['alg' => 'RS256'], [...$payload, 'iss' => 'https://other.example']), JwtException::UNKNOWN_ISSUER];
        yield 'forged signature' => [static fn(): string => $forge(['alg' => 'RS256'], $payload), JwtException::BAD_SIGNATURE];
        yield 'wrong key' => [static fn(Rs256Test $t): string => $t->signer('other')->sign([Claims::LOGISTICS_AGENT_URI => self::AGENT], 60), JwtException::BAD_SIGNATURE];
        yield 'tampered payload' => [static function (Rs256Test $t): string {
            $parts = explode('.', $t->signer()->sign(['sub' => 'a'], 60));
            $parts[1] = Jwk::base64UrlEncode(str_replace('"sub":"a"', '"sub":"b"', Jwk::base64UrlDecode($parts[1])));

            return implode('.', $parts);
        }, JwtException::BAD_SIGNATURE];
        yield 'tampered signature' => [static function (Rs256Test $t): string {
            $parts = explode('.', $t->signer()->sign([], 60));
            $parts[2] = Jwk::base64UrlEncode(strrev(Jwk::base64UrlDecode($parts[2])));

            return implode('.', $parts);
        }, JwtException::BAD_SIGNATURE];
        yield 'jku and embedded jwk are ignored, so an unknown key fails' => [static function (Rs256Test $t): string {
            $parts = explode('.', $t->signer('other')->sign([], 60));
            $parts[0] = Jwk::base64UrlEncode(json_encode(['alg' => 'RS256', 'jku' => 'https://evil.example/jwks.json', 'jwk' => $t->signer('other')->publicJwk()], JSON_THROW_ON_ERROR));

            return implode('.', $parts);
        }, JwtException::BAD_SIGNATURE];
        yield 'expired' => [static fn(Rs256Test $t): string => $t->signer(clock: new FixedClock('2026-10-02T11:00:00Z'))->sign([], 60), JwtException::EXPIRED];
        yield 'not yet valid' => [static fn(Rs256Test $t): string => $t->signer(clock: new FixedClock('2026-10-02T13:00:00Z'))->sign([], 60), JwtException::NOT_YET_VALID];
        yield 'wrong audience' => [static fn(Rs256Test $t): string => $t->signer()->sign(['aud' => 'someone-else'], 60), JwtException::AUDIENCE_MISMATCH];
        yield 'no audience when one is expected' => [static fn(Rs256Test $t): string => $t->signer()->sign([], 60), JwtException::AUDIENCE_MISMATCH];
    }

    /**
     * @param callable(Rs256Test): string $token
     */
    #[DataProvider('rejectedTokens')]
    public function testRejects(callable $token, string $reason): void
    {
        $verifier = $this->verifier(audience: \in_array($reason, [JwtException::AUDIENCE_MISMATCH], true) ? 'one-record' : null);
        try {
            $verifier->verify($token($this));
            self::fail('expected rejection: ' . $reason);
        } catch (JwtException $e) {
            self::assertSame($reason, $e->reason, $e->getMessage());
        }
    }

    public function testLeewayAndAudienceList(): void
    {
        $clock = new FixedClock('2026-10-02T12:00:00Z');
        // Issued 20 seconds in the future: within the 30 second leeway.
        $token = $this->signer(clock: new FixedClock('2026-10-02T12:00:20Z'))->sign(['aud' => ['a', 'one-record']], 60);
        self::assertSame(['a', 'one-record'], $this->verifier($clock, 'one-record')->verify($token)->audience());

        // Expired 20 seconds ago: still within leeway.
        $token = $this->signer(clock: new FixedClock('2026-10-02T11:58:40Z'))->sign([], 60);
        self::assertSame(self::ISSUER, $this->verifier($clock)->verify($token)->issuer());

        $strict = new Rs256Verifier(new StaticKeyResolver([self::ISSUER => TestKeys::pair()['public']]), $clock, leewaySeconds: 0);
        $this->expectException(JwtException::class);
        $strict->verify($token);
    }

    public function testMissingExpIsRejectedEvenWithAValidSignature(): void
    {
        // Sign a payload without exp by hand with the real key.
        $header = Jwk::base64UrlEncode('{"alg":"RS256"}');
        $payload = Jwk::base64UrlEncode(json_encode(['iss' => self::ISSUER], JSON_THROW_ON_ERROR));
        $key = openssl_pkey_get_private(TestKeys::pair()['private']);
        self::assertNotFalse($key);
        $signature = '';
        openssl_sign($header . '.' . $payload, $signature, $key, OPENSSL_ALGO_SHA256);
        self::assertIsString($signature);
        try {
            $this->verifier()->verify($header . '.' . $payload . '.' . Jwk::base64UrlEncode($signature));
            self::fail();
        } catch (JwtException $e) {
            self::assertSame(JwtException::MISSING_CLAIM, $e->reason);
        }
    }

    public function testClaimsReadTimestampsLeniently(): void
    {
        $claims = new Claims(['exp' => '2026-10-02T12:00:00Z', 'nbf' => '1790000000', 'iat' => 1.5, 'aud' => ['x', 1, 'y'], 'iss' => 1]);
        self::assertSame((new DateTimeImmutable('2026-10-02T12:00:00Z'))->getTimestamp(), $claims->expiresAt());
        self::assertSame(1790000000, $claims->notBefore());
        self::assertSame(1, $claims->issuedAt());
        self::assertSame(['x', 'y'], $claims->audience());
        self::assertNull($claims->issuer());
        self::assertNull((new Claims(['exp' => 'soon']))->expiresAt());
        self::assertNull((new Claims([Claims::LOGISTICS_AGENT_URI => '']))->logisticsAgentUri());
    }

    public function testSignerRefusesWeakOrInvalidKeys(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Rs256Signer('not a key', self::ISSUER, new FixedClock());
    }

    public function testSignerAcceptsEscapedNewlinesAndExposesThePublicKey(): void
    {
        $pair = TestKeys::pair();
        $signer = new Rs256Signer(str_replace("\n", '\n', $pair['private']), self::ISSUER, new FixedClock(), 'k1');

        self::assertSame($pair['public'], $signer->publicKeyPem());
        self::assertSame('k1', $signer->keyId());
        self::assertSame(self::ISSUER, $signer->issuer());
        $jwk = $signer->publicJwk();
        self::assertSame(['kty' => 'RSA', 'use' => 'sig', 'alg' => 'RS256', 'kid' => 'k1'], array_intersect_key($jwk, ['kty' => 1, 'use' => 1, 'alg' => 1, 'kid' => 1]));
        self::assertSame($pair['public'], Jwk::rsaToPem($jwk), 'JWK to PEM reproduces the original SubjectPublicKeyInfo');
        $header = json_decode(Jwk::base64UrlDecode(explode('.', $signer->sign([], 1))[0]), true);
        self::assertSame(['alg' => 'RS256', 'typ' => 'JWT', 'kid' => 'k1'], $header);
    }

    public function testJwkConversionRejectsNonRsaKeys(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Jwk::rsaToPem(['kty' => 'EC', 'crv' => 'P-256']);
    }

    public function testStaticResolverNarrowsByKeyId(): void
    {
        $resolver = new StaticKeyResolver([self::ISSUER => ['k1' => 'PEM1', 'k2' => 'PEM2'], 'https://b.example' => 'PEMB']);

        self::assertSame(['PEM2'], $resolver->publicKeys(self::ISSUER, 'k2'));
        self::assertSame(['PEM1', 'PEM2'], $resolver->publicKeys(self::ISSUER, 'unknown'), 'an unknown kid falls back to every key of the issuer');
        self::assertSame(['PEMB'], $resolver->publicKeys('https://b.example', null));
        self::assertSame([], $resolver->publicKeys('https://c.example', null));
    }

    public function testJwksResolverFetchesCachesAndRefreshesOnUnknownKid(): void
    {
        $factory = new Psr17Factory();
        $signer1 = $this->signer('default', 'k1');
        $signer2 = $this->signer('other', 'k2');
        $jwks = static fn(array $keys): \Nyholm\Psr7\Response => new \Nyholm\Psr7\Response(200, ['Content-Type' => 'application/json'], json_encode(['keys' => $keys], JSON_THROW_ON_ERROR));
        $http = (new FakeHttpClient())
            ->queue($jwks([$signer1->publicJwk(), ['kty' => 'EC', 'kid' => 'ec'], ['kty' => 'RSA', 'kid' => 'enc', 'use' => 'enc', 'n' => 'AQ', 'e' => 'AQAB'], ['kty' => 'RSA', 'kid' => 'broken', 'n' => '', 'e' => '']]))
            ->queue($jwks([$signer1->publicJwk(), $signer2->publicJwk()]));
        $cache = new ArrayCache();
        $resolver = new JwksKeyResolver([self::ISSUER => 'https://auth.example.com/keys', 'https://wk.example' => null], $http, $factory, $cache, 600);
        $verifier = new Rs256Verifier($resolver, new FixedClock());

        self::assertSame(self::ISSUER, $verifier->verify($signer1->sign([], 60))->issuer());
        self::assertCount(1, $http->requests);
        self::assertSame('https://auth.example.com/keys', (string) $http->requests[0]->getUri());
        self::assertSame(1, $cache->writes);

        self::assertSame(self::ISSUER, $verifier->verify($signer1->sign([], 60))->issuer());
        self::assertCount(1, $http->requests, 'served from the cache');

        // A token signed with a rotated key: one refresh, then success.
        self::assertSame(self::ISSUER, $verifier->verify($signer2->sign([], 60))->issuer());
        self::assertCount(2, $http->requests);
        self::assertSame(2, $cache->writes);

        self::assertSame([], $resolver->publicKeys('https://unknown.example', null));
        self::assertCount(2, $http->requests, 'untrusted issuers are never fetched');

        $http->queue(new \Nyholm\Psr7\Response(500));
        self::assertSame([], $resolver->publicKeys('https://wk.example', null));
        $last = $http->requests[\count($http->requests) - 1];
        self::assertInstanceOf(\Psr\Http\Message\RequestInterface::class, $last);
        self::assertSame('https://wk.example/.well-known/jwks.json', (string) $last->getUri());
    }

    public function testJwksResolverSurvivesTransportFailures(): void
    {
        $exception = new class ('down') extends RuntimeException implements \Psr\Http\Client\ClientExceptionInterface {};
        $http = (new FakeHttpClient())->queue($exception);
        $resolver = new JwksKeyResolver([self::ISSUER => null], $http, new Psr17Factory(), new ArrayCache());

        self::assertSame([], $resolver->publicKeys(self::ISSUER, null));
    }
}
