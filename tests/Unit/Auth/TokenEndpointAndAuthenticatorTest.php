<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Auth;

use LambdaTwelve\OneRecord\Auth\ClientCredentialsVerifier;
use LambdaTwelve\OneRecord\Auth\InMemoryClientCredentials;
use LambdaTwelve\OneRecord\Auth\Jwt\Claims;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Signer;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Verifier;
use LambdaTwelve\OneRecord\Auth\Jwt\StaticKeyResolver;
use LambdaTwelve\OneRecord\Auth\JwtAuthenticator;
use LambdaTwelve\OneRecord\Auth\TokenEndpoint;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Testing\FixedClock;
use LambdaTwelve\OneRecord\Testing\TestKeys;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(TokenEndpoint::class)]
#[CoversClass(InMemoryClientCredentials::class)]
#[CoversClass(JwtAuthenticator::class)]
#[CoversClass(Agent::class)]
#[UsesClass(Rs256Signer::class)]
#[UsesClass(Rs256Verifier::class)]
#[UsesClass(StaticKeyResolver::class)]
#[UsesClass(Claims::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Auth\Jwt\Jwk::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Auth\Jwt\JwtException::class)]
#[UsesClass(Iri::class)]
final class TokenEndpointAndAuthenticatorTest extends TestCase
{
    private const string ISSUER = 'https://1r.example.com';
    private const string AGENT = 'https://1r.lh.example/logistics-objects/_data-holder';

    private FixedClock $clock;
    private Rs256Signer $signer;
    private InMemoryClientCredentials $credentials;
    private TokenEndpoint $endpoint;

    protected function setUp(): void
    {
        $this->clock = new FixedClock();
        $this->signer = new Rs256Signer(TestKeys::pair()['private'], self::ISSUER, $this->clock, 'k1');
        $this->credentials = new InMemoryClientCredentials();
        $this->credentials->add('or_lh', 'lh-secret', new Iri(self::AGENT));
        $factory = new Psr17Factory();
        $this->endpoint = new TokenEndpoint($this->credentials, $this->signer, $factory, $factory, 900, 'one-record');
    }

    /**
     * @param array<string, string> $form
     * @param array<string, string> $headers
     */
    private function post(array $form = [], array $headers = [], string $method = 'POST'): ResponseInterface
    {
        $request = new ServerRequest($method, 'https://1r.example.com/oauth/token', ['Content-Type' => 'application/x-www-form-urlencoded', ...$headers], http_build_query($form));

        return $this->endpoint->handle($request);
    }

    /**
     * @return array<string, mixed>
     */
    private static function json(ResponseInterface $response): array
    {
        $decoded = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    public function testIssuesATokenForValidPostCredentials(): void
    {
        $response = $this->post(['grant_type' => 'client_credentials', 'client_id' => 'or_lh', 'client_secret' => 'lh-secret']);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('no-store', $response->getHeaderLine('Cache-Control'));
        self::assertSame('no-cache', $response->getHeaderLine('Pragma'));
        self::assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        $body = self::json($response);
        self::assertSame('Bearer', $body['token_type']);
        self::assertSame(900, $body['expires_in']);
        self::assertIsString($body['access_token']);

        $claims = (new Rs256Verifier(new StaticKeyResolver([self::ISSUER => TestKeys::pair()['public']]), $this->clock, 'one-record'))->verify($body['access_token']);
        self::assertSame('or_lh', $claims->subject());
        self::assertSame(self::AGENT, $claims->logisticsAgentUri());
        self::assertSame(['one-record'], $claims->audience());
        self::assertSame((float) ($this->clock->now()->getTimestamp() + 900), $claims->expiresAt());
    }

    public function testAcceptsBasicAuthenticationAndParsedBodies(): void
    {
        $response = $this->post(['grant_type' => 'client_credentials'], ['Authorization' => 'Basic ' . base64_encode('or_lh:lh-secret')]);
        self::assertSame(200, $response->getStatusCode());

        $request = (new ServerRequest('POST', 'https://1r.example.com/oauth/token'))->withParsedBody(['grant_type' => 'client_credentials', 'client_id' => 'or_lh', 'client_secret' => 'lh-secret']);
        self::assertSame(200, $this->endpoint->handle($request)->getStatusCode());
    }

    public function testRefusesBadRequests(): void
    {
        $response = $this->post(['grant_type' => 'client_credentials', 'client_id' => 'or_lh', 'client_secret' => 'wrong']);
        self::assertSame(401, $response->getStatusCode());
        self::assertSame('invalid_client', self::json($response)['error']);
        self::assertFalse($response->hasHeader('WWW-Authenticate'), 'no Basic challenge when credentials came in the body');

        $response = $this->post(['grant_type' => 'client_credentials'], ['Authorization' => 'Basic ' . base64_encode('or_lh:wrong')]);
        self::assertSame(401, $response->getStatusCode());
        self::assertStringStartsWith('Basic', $response->getHeaderLine('WWW-Authenticate'));

        self::assertSame(401, $this->post(['grant_type' => 'client_credentials', 'client_id' => 'nobody', 'client_secret' => 'x'])->getStatusCode());
        self::assertSame('unsupported_grant_type', self::json($this->post(['grant_type' => 'password', 'client_id' => 'or_lh', 'client_secret' => 'lh-secret']))['error']);
        self::assertSame('invalid_request', self::json($this->post(['grant_type' => 'client_credentials']))['error']);
        $response = $this->post([], [], 'GET');
        self::assertSame(405, $response->getStatusCode());
        self::assertSame('POST', $response->getHeaderLine('Allow'));
        self::assertSame(400, $this->post(['grant_type' => 'client_credentials'], ['Authorization' => 'Basic !!!'])->getStatusCode());
    }

    public function testVerifierInterfaceIsHonoured(): void
    {
        $custom = new class implements ClientCredentialsVerifier {
            public function verify(string $clientId, string $clientSecret): ?Iri
            {
                return $clientId === 'ok' ? new Iri('https://x.example/logistics-objects/a') : null;
            }
        };
        $factory = new Psr17Factory();
        $endpoint = new TokenEndpoint($custom, $this->signer, $factory, $factory);
        $request = new ServerRequest('POST', 'https://1r.example.com/oauth/token', ['Content-Type' => 'application/x-www-form-urlencoded'], 'grant_type=client_credentials&client_id=ok&client_secret=any');

        $body = self::json($endpoint->handle($request));
        self::assertSame(3600, $body['expires_in']);
        self::assertIsString($body['access_token']);
        $claims = (new Rs256Verifier(new StaticKeyResolver([self::ISSUER => TestKeys::pair()['public']]), $this->clock))->verify($body['access_token']);
        self::assertSame([], $claims->audience(), 'no audience unless configured');
    }

    public function testAuthenticatorTurnsBearerTokensIntoAgents(): void
    {
        $verifier = new Rs256Verifier(new StaticKeyResolver([self::ISSUER => TestKeys::pair()['public']]), $this->clock, 'one-record');
        $authenticator = new JwtAuthenticator($verifier);
        $token = $this->signer->sign(['sub' => 'or_lh', 'aud' => 'one-record', Claims::LOGISTICS_AGENT_URI => self::AGENT], 60);

        $agent = $authenticator->authenticate(new ServerRequest('GET', 'https://1r.example.com/', ['Authorization' => 'Bearer ' . $token]));
        self::assertNotNull($agent);
        self::assertSame(self::AGENT, $agent->iri->value);
        self::assertSame(self::ISSUER, $agent->issuer);
        self::assertSame('or_lh', $agent->claims['sub']);
        self::assertTrue($agent->is(self::AGENT));
        self::assertTrue($agent->is(new Iri(self::AGENT)));
        self::assertFalse($agent->is('https://other.example/x'));

        self::assertNull($authenticator->authenticate(new ServerRequest('GET', 'https://1r.example.com/')), 'no header');
        self::assertNull($authenticator->authenticate(new ServerRequest('GET', 'https://1r.example.com/', ['Authorization' => 'Basic abc'])), 'not a bearer');
        self::assertNull($authenticator->authenticate(new ServerRequest('GET', 'https://1r.example.com/', ['Authorization' => 'Bearer not.a.token'])), 'malformed');
        self::assertNull($authenticator->authenticate(new ServerRequest('GET', 'https://1r.example.com/', ['Authorization' => 'Bearer ' . $this->signer->sign(['aud' => 'one-record'], 60)])), 'no logistics_agent_uri');
        self::assertNull($authenticator->authenticate(new ServerRequest('GET', 'https://1r.example.com/', ['Authorization' => 'Bearer ' . $this->signer->sign(['aud' => 'one-record', Claims::LOGISTICS_AGENT_URI => 'not an iri'], 60)])), 'invalid agent IRI');
        self::assertNull($authenticator->authenticate(new ServerRequest('GET', 'https://1r.example.com/', ['Authorization' => 'Bearer ' . $this->signer->sign([Claims::LOGISTICS_AGENT_URI => self::AGENT], 60)])), 'wrong audience');
        self::assertSame($token, JwtAuthenticator::bearerToken(new ServerRequest('GET', '/', ['Authorization' => 'bearer ' . $token . ' '])));
    }

    public function testInMemoryCredentialsDoNotRevealUnknownClients(): void
    {
        self::assertNull($this->credentials->verify('or_lh', 'wrong'));
        self::assertNull($this->credentials->verify('unknown', 'lh-secret'));
        self::assertSame(self::AGENT, $this->credentials->verify('or_lh', 'lh-secret')?->value);
    }

    public function testR7009OneAuthenticationMethodAndNoRepeatedParameters(): void
    {
        // Valid Basic credentials for one client and body credentials for another: RFC 6749 §2.3 says invalid_request.
        $response = $this->post(['grant_type' => 'client_credentials', 'client_id' => 'or_other', 'client_secret' => 'x'], ['Authorization' => 'Basic ' . base64_encode('or_lh:lh-secret')]);
        self::assertSame(400, $response->getStatusCode());
        self::assertSame('invalid_request', self::json($response)['error']);
        // Basic plus a body client_id alone is still two methods.
        self::assertSame(400, $this->post(['grant_type' => 'client_credentials', 'client_id' => 'or_lh'], ['Authorization' => 'Basic ' . base64_encode('or_lh:lh-secret')])->getStatusCode());

        // A repeated parameter (§3.2) is refused rather than collapsed to its last value.
        $raw = new ServerRequest('POST', 'https://1r.example.com/oauth/token', ['Content-Type' => 'application/x-www-form-urlencoded'], 'grant_type=client_credentials&client_id=or_lh&client_secret=wrong&client_secret=lh-secret');
        $response = $this->endpoint->handle($raw);
        self::assertSame(400, $response->getStatusCode());
        self::assertSame('invalid_request', self::json($response)['error']);

        // One method, once: still a token.
        self::assertSame(200, $this->post(['grant_type' => 'client_credentials'], ['Authorization' => 'Basic ' . base64_encode('or_lh:lh-secret')])->getStatusCode());
        self::assertSame(200, $this->post(['grant_type' => 'client_credentials', 'client_id' => 'or_lh', 'client_secret' => 'lh-secret'])->getStatusCode());
    }
}
