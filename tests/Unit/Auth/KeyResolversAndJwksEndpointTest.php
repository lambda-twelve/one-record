<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Auth;

use DateInterval;
use LambdaTwelve\OneRecord\Auth\JwksEndpoint;
use LambdaTwelve\OneRecord\Auth\Jwt\ChainKeyResolver;
use LambdaTwelve\OneRecord\Auth\Jwt\JwksKeyResolver;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Signer;
use LambdaTwelve\OneRecord\Auth\Jwt\StaticKeyResolver;
use LambdaTwelve\OneRecord\Testing\FakeHttpClient;
use LambdaTwelve\OneRecord\Testing\FixedClock;
use LambdaTwelve\OneRecord\Testing\TestKeys;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;

final class KeyResolversAndJwksEndpointTest extends TestCase
{
    public function testTheChainAsksResolversInOrderAndTheFirstThatKnowsTheIssuerWins(): void
    {
        $pinned = TestKeys::pair('pinned');
        $other = TestKeys::pair('other');
        $chain = new ChainKeyResolver(
            new StaticKeyResolver(['https://pinned.example' => $pinned['public']]),
            new StaticKeyResolver(['https://pinned.example' => $other['public'], 'https://second.example' => $other['public']]),
        );

        self::assertSame([$pinned['public']], $chain->publicKeys('https://pinned.example', null), 'the first resolver answers');
        self::assertSame([$other['public']], $chain->publicKeys('https://second.example', null), 'the second is consulted when the first does not know the issuer');
        self::assertSame([], $chain->publicKeys('https://nobody.example', null));
    }

    public function testJwksWorksWithoutACacheAndSurvivesABrokenOne(): void
    {
        $factory = new Psr17Factory();
        $signer = new Rs256Signer(TestKeys::pair('jwks')['private'], 'https://idp.example', new FixedClock(), 'k1');
        $jwks = json_encode(['keys' => [$signer->publicJwk()]], JSON_THROW_ON_ERROR);

        $http = (new FakeHttpClient())->queue(new Response(200, [], $jwks))->queue(new Response(200, [], $jwks));
        $resolver = new JwksKeyResolver(['https://idp.example' => 'https://idp.example/jwks'], $http, $factory);
        self::assertCount(1, $resolver->publicKeys('https://idp.example', 'k1'));
        self::assertCount(1, $resolver->publicKeys('https://idp.example', 'k1'));
        self::assertCount(2, $http->requests, 'no cache: every resolution fetches');

        $broken = new class implements CacheInterface {
            public function get(string $key, mixed $default = null): mixed
            {
                throw new RuntimeException('redis down');
            }

            public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
            {
                throw new RuntimeException('redis down');
            }

            public function delete(string $key): bool
            {
                return true;
            }

            public function clear(): bool
            {
                return true;
            }

            public function getMultiple(iterable $keys, mixed $default = null): iterable
            {
                return [];
            }

            /**
             * @param iterable<mixed, mixed> $values
             */
            public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
            {
                return true;
            }

            public function deleteMultiple(iterable $keys): bool
            {
                return true;
            }

            public function has(string $key): bool
            {
                return false;
            }
        };
        $http = (new FakeHttpClient())->queue(new Response(200, [], $jwks));
        $resolver = new JwksKeyResolver(['https://idp.example' => 'https://idp.example/jwks'], $http, $factory, $broken);
        self::assertCount(1, $resolver->publicKeys('https://idp.example', 'k1'), 'a failing cache is bypassed, not fatal');
    }

    public function testTheJwksEndpointPublishesTheSigningKeys(): void
    {
        $factory = new Psr17Factory();
        $current = new Rs256Signer(TestKeys::pair('current')['private'], 'https://1r.example.com', new FixedClock(), 'current');
        $retiring = new Rs256Signer(TestKeys::pair('retiring')['private'], 'https://1r.example.com', new FixedClock(), 'retiring');
        $endpoint = new JwksEndpoint($factory, $factory, 600, $current, $retiring);

        $response = $endpoint->handle(new ServerRequest('GET', 'https://1r.example.com/.well-known/jwks.json'));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('public, max-age=600', $response->getHeaderLine('Cache-Control'));
        $document = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($document);
        self::assertIsArray($document['keys']);
        self::assertSame(['current', 'retiring'], array_column($document['keys'], 'kid'));

        // A verifier fed this document accepts a token from the current key.
        $http = (new FakeHttpClient())->queue(new Response(200, [], (string) $response->getBody()));
        $resolver = new JwksKeyResolver(['https://1r.example.com' => 'https://1r.example.com/.well-known/jwks.json'], $http, $factory);
        self::assertSame([$current->publicKeyPem()], $resolver->publicKeys('https://1r.example.com', 'current'));

        self::assertSame('', (string) $endpoint->handle(new ServerRequest('HEAD', 'https://1r.example.com/.well-known/jwks.json'))->getBody());
        self::assertSame(405, $endpoint->handle(new ServerRequest('POST', 'https://1r.example.com/.well-known/jwks.json'))->getStatusCode());
    }

    public function testAr025UnknownKeyIdsRefreshAtMostOncePerCooldown(): void
    {
        $factory = new Psr17Factory();
        $signer = new Rs256Signer(TestKeys::pair('jwks')['private'], 'https://idp.example', new FixedClock(), 'k1');
        $jwks = json_encode(['keys' => [$signer->publicJwk()]], JSON_THROW_ON_ERROR);
        $http = new FakeHttpClient();
        for ($i = 0; $i < 6; $i++) {
            $http->queue(new Response(200, [], $jwks));
        }
        $resolver = new JwksKeyResolver(['https://idp.example' => 'https://idp.example/jwks'], $http, $factory, new \LambdaTwelve\OneRecord\Testing\ArrayCache());

        self::assertCount(1, $resolver->publicKeys('https://idp.example', 'k1'), 'warm-up');
        foreach (['bogus1', 'bogus2', 'bogus3', 'bogus4', 'bogus5'] as $kid) {
            $resolver->publicKeys('https://idp.example', $kid);
        }
        self::assertCount(2, $http->requests, 'one fetch to warm up, one refresh for the first unknown key id, then the cooldown holds');
    }

    public function testR2010TheCooldownSurvivesResolverReconstruction(): void
    {
        $factory = new Psr17Factory();
        $signer = new Rs256Signer(TestKeys::pair('jwks')['private'], 'https://idp.example', new FixedClock(), 'k1');
        $jwks = json_encode(['keys' => [$signer->publicJwk()]], JSON_THROW_ON_ERROR);
        $http = new FakeHttpClient();
        for ($i = 0; $i < 7; $i++) {
            $http->queue(new Response(200, [], $jwks));
        }
        $cache = new \LambdaTwelve\OneRecord\Testing\ArrayCache();
        $make = static fn(): JwksKeyResolver => new JwksKeyResolver(['https://idp.example' => 'https://idp.example/jwks'], $http, $factory, $cache);

        self::assertCount(1, $make()->publicKeys('https://idp.example', 'k1'), 'warm-up');
        foreach (['bogus1', 'bogus2', 'bogus3', 'bogus4', 'bogus5'] as $kid) {
            $make()->publicKeys('https://idp.example', $kid);
        }
        self::assertCount(2, $http->requests, 'one refresh for the first unknown key id; later request-scoped resolvers see the cooldown in the cache');
    }

    public function testR3TheCooldownExpiresWithTheCacheEntryAndARotatedKeyIsThenFetched(): void
    {
        $factory = new Psr17Factory();
        $clock = new FixedClock('2026-10-02T12:00:00Z');
        $old = new Rs256Signer(TestKeys::pair('jwks')['private'], 'https://idp.example', $clock, 'k1');
        $new = new Rs256Signer(TestKeys::pair('rotated')['private'], 'https://idp.example', $clock, 'k2');
        $http = new FakeHttpClient();
        $http->queue(new Response(200, [], json_encode(['keys' => [$old->publicJwk()]], JSON_THROW_ON_ERROR)));
        $http->queue(new Response(200, [], json_encode(['keys' => [$old->publicJwk()]], JSON_THROW_ON_ERROR)));
        $http->queue(new Response(200, [], json_encode(['keys' => [$old->publicJwk(), $new->publicJwk()]], JSON_THROW_ON_ERROR)));
        // A PSR-16 cache that honours TTLs against the test clock, unlike ArrayCache.
        $cache = new class ($clock) implements CacheInterface {
            /** @var array<string, array{mixed, ?int}> */
            private array $items = [];

            public function __construct(private readonly FixedClock $clock) {}

            public function get(string $key, mixed $default = null): mixed
            {
                if (!isset($this->items[$key])) {
                    return $default;
                }
                [$value, $expires] = $this->items[$key];
                if ($expires !== null && $expires <= $this->clock->now()->getTimestamp()) {
                    unset($this->items[$key]);

                    return $default;
                }

                return $value;
            }

            public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
            {
                $seconds = $ttl instanceof DateInterval ? (int) $ttl->format('%s') + 60 * (int) $ttl->format('%i') : $ttl;
                $this->items[$key] = [$value, $seconds === null ? null : $this->clock->now()->getTimestamp() + $seconds];

                return true;
            }

            public function delete(string $key): bool
            {
                unset($this->items[$key]);

                return true;
            }

            public function clear(): bool
            {
                $this->items = [];

                return true;
            }

            public function getMultiple(iterable $keys, mixed $default = null): iterable
            {
                $out = [];
                foreach ($keys as $key) {
                    $out[$key] = $this->get($key, $default);
                }

                return $out;
            }

            /**
             * @param iterable<mixed, mixed> $values
             */
            public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
            {
                foreach ($values as $key => $value) {
                    $this->set(\is_string($key) ? $key : '', $value, $ttl);
                }

                return true;
            }

            public function deleteMultiple(iterable $keys): bool
            {
                foreach ($keys as $key) {
                    $this->delete($key);
                }

                return true;
            }

            public function has(string $key): bool
            {
                return $this->get($key) !== null;
            }
        };
        $make = static fn(): JwksKeyResolver => new JwksKeyResolver(['https://idp.example' => 'https://idp.example/jwks'], $http, $factory, $cache, 3600, refreshCooldownSeconds: 60);

        self::assertCount(1, $make()->publicKeys('https://idp.example', 'k1'));
        // An unknown kid refreshes once, then every known key is offered for the signature check.
        $make()->publicKeys('https://idp.example', 'k2');
        self::assertCount(2, $http->requests, 'not yet published: one refresh');
        $make()->publicKeys('https://idp.example', 'k2');
        self::assertCount(2, $http->requests, 'inside the cooldown: no fetch');
        $clock->advance('+61 seconds');
        $keys = $make()->publicKeys('https://idp.example', 'k2');
        self::assertCount(3, $http->requests, 'cooldown over: the rotated key is fetched');
        self::assertCount(1, $keys, 'the rotated key is now known by its kid');
        $modulus = static function (string $pem): string {
            $key = openssl_pkey_get_public($pem);
            $details = $key === false ? false : openssl_pkey_get_details($key);
            $n = \is_array($details) && \is_array($details['rsa'] ?? null) ? ($details['rsa']['n'] ?? null) : null;

            return \is_string($n) ? bin2hex($n) : throw new RuntimeException('not an RSA key');
        };
        self::assertSame($modulus(TestKeys::pair('rotated')['public']), $modulus($keys[0]), 'and it is the rotated key, not the old one offered again');
    }
}
