<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Client;

use LambdaTwelve\OneRecord\Client\ClientCredentialsTokenProvider;
use LambdaTwelve\OneRecord\Client\ClientException;
use LambdaTwelve\OneRecord\Testing\ArrayCache;
use LambdaTwelve\OneRecord\Testing\FakeHttpClient;
use LambdaTwelve\OneRecord\Testing\FixedClock;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientCredentialsTokenProviderTest extends TestCase
{
    private const string TOKEN_URL = 'https://1r.partner.example/oauth/token';

    private function tokenResponse(string $token, int $expiresIn = 3600): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode(['access_token' => $token, 'token_type' => 'Bearer', 'expires_in' => $expiresIn], JSON_THROW_ON_ERROR));
    }

    public function testFetchesWithClientSecretPostAndCachesUntilShortlyBeforeExpiry(): void
    {
        $http = new FakeHttpClient();
        $clock = new FixedClock('2026-10-02T12:00:00Z');
        $cache = new ArrayCache();
        $factory = new Psr17Factory();
        $provider = new ClientCredentialsTokenProvider($http, $factory, $factory, $clock, self::TOKEN_URL, 'awback', 's3cret', $cache, refreshMarginSeconds: 60);
        $http->queue($this->tokenResponse('first', 600))->queue($this->tokenResponse('second', 600));

        self::assertSame('first', $provider->token('https://1r.partner.example'));
        self::assertSame('first', $provider->token('https://1r.partner.example'), 'served from the cache');
        self::assertCount(1, $http->requests);
        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame(self::TOKEN_URL, (string) $request->getUri());
        self::assertSame('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
        parse_str((string) $request->getBody(), $fields);
        self::assertSame(['grant_type' => 'client_credentials', 'client_id' => 'awback', 'client_secret' => 's3cret'], $fields);

        $clock->advance('+8 minutes 59 seconds');
        self::assertSame('first', $provider->token('https://1r.partner.example'), 'still a minute and a second away from expiry');
        $clock->advance('+2 seconds');
        self::assertSame('second', $provider->token('https://1r.partner.example'), 'refreshed within the margin');
        self::assertCount(2, $http->requests);
    }

    public function testBasicAuthenticationAndScope(): void
    {
        $http = new FakeHttpClient();
        $factory = new Psr17Factory();
        $provider = new ClientCredentialsTokenProvider($http, $factory, $factory, new FixedClock(), self::TOKEN_URL, 'awback', 's3cret', scope: 'one-record', basicAuth: true);
        $http->queue($this->tokenResponse('t'));

        $provider->token('https://1r.partner.example');

        $request = $http->requests[0];
        self::assertSame('Basic ' . base64_encode('awback:s3cret'), $request->getHeaderLine('Authorization'));
        parse_str((string) $request->getBody(), $fields);
        self::assertSame(['grant_type' => 'client_credentials', 'scope' => 'one-record'], $fields, 'the secret is not repeated in the body');
    }

    public function testWithoutACacheTheTokenIsKeptInMemory(): void
    {
        $http = new FakeHttpClient();
        $factory = new Psr17Factory();
        $provider = new ClientCredentialsTokenProvider($http, $factory, $factory, new FixedClock(), self::TOKEN_URL, 'awback', 's3cret');
        $http->queue($this->tokenResponse('t'));

        self::assertSame('t', $provider->token('x'));
        self::assertSame('t', $provider->token('x'));
        self::assertCount(1, $http->requests);
    }

    public function testFailuresAreExplainedWithoutEchoingTheBody(): void
    {
        $factory = new Psr17Factory();
        $cases = [
            [new Response(401, [], '{"error": "invalid_client", "error_description": "secret s3cret is wrong"}'), 'answered 401 (invalid_client)'],
            [new Response(500, [], '<html>oops</html>'), 'answered 500.'],
            [new Response(200, [], 'not json'), 'did not answer with JSON'],
            [new Response(200, [], '{"token_type": "Bearer"}'), 'without an access_token'],
            [new Response(200, [], '{"access_token": "x", "token_type": "MAC"}'), 'type "MAC"'],
        ];
        foreach ($cases as [$response, $expected]) {
            $http = (new FakeHttpClient())->queue($response);
            $provider = new ClientCredentialsTokenProvider($http, $factory, $factory, new FixedClock(), self::TOKEN_URL, 'awback', 's3cret');
            try {
                $provider->token('x');
                self::fail('expected a ClientException for ' . $expected);
            } catch (ClientException $e) {
                self::assertStringContainsString($expected, $e->getMessage());
                self::assertStringNotContainsString('s3cret is wrong', $e->getMessage());
            }
        }
    }

    public function testAr019ReservedCharactersSurviveBasicAuthenticationEndToEnd(): void
    {
        $factory = new Psr17Factory();
        $credentials = new \LambdaTwelve\OneRecord\Auth\InMemoryClientCredentials();
        $credentials->add('a:b', 'p%41 +x', new \LambdaTwelve\OneRecord\Rdf\Iri('https://1r.example.com/logistics-objects/forwarder'));
        $signer = new \LambdaTwelve\OneRecord\Auth\Jwt\Rs256Signer(\LambdaTwelve\OneRecord\Testing\TestKeys::pair('basic')['private'], 'https://1r.example.com', new FixedClock());
        $endpoint = new \LambdaTwelve\OneRecord\Auth\TokenEndpoint($credentials, $signer, $factory, $factory);
        $http = new \LambdaTwelve\OneRecord\Testing\InProcessHttpClient($endpoint, $factory, $factory);

        $provider = new ClientCredentialsTokenProvider($http, $factory, $factory, new FixedClock(), 'https://1r.example.com/oauth/token', 'a:b', 'p%41 +x', basicAuth: true);
        self::assertNotSame('', $provider->token('https://1r.example.com'), 'RFC 6749 form-encoding on both sides');

        // A conforming external client that form-encodes by hand is accepted too.
        $request = $factory->createServerRequest('POST', 'https://1r.example.com/oauth/token')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('Authorization', 'Basic ' . base64_encode('a%3Ab:p%2541+%2Bx'))
            ->withBody($factory->createStream('grant_type=client_credentials'));
        self::assertSame(200, $endpoint->handle($request)->getStatusCode());
    }
}
