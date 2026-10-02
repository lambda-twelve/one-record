<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\InMemory\InMemoryServer;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\UuidIdGenerator;
use LambdaTwelve\OneRecord\Tests\Support\FixedClock;
use LambdaTwelve\OneRecord\Tests\Support\HeaderAuthenticator;
use LambdaTwelve\OneRecord\Tests\Support\RecordingDispatcher;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Drives the complete in-memory server through PSR-7 requests.
 */
abstract class ServerTestCase extends TestCase
{
    protected const string BASE = 'https://1r.example.com';
    protected const string HOLDER = 'https://1r.example.com/logistics-objects/holder';
    protected const string PARTNER = 'https://1r.partner.example/logistics-objects/partner';
    protected const string STRANGER = 'https://1r.other.example/logistics-objects/stranger';

    protected FixedClock $clock;
    protected RecordingDispatcher $dispatcher;
    protected InMemoryServer $server;

    protected function setUp(): void
    {
        $this->clock = new FixedClock('2026-10-02T12:00:00Z');
        $this->dispatcher = new RecordingDispatcher();
        $this->server = $this->makeServer();
    }

    protected function makeServer(Decision $denial = Decision::Forbid, string $basePath = '', bool $bulkEvents = false, ?\LambdaTwelve\OneRecord\Server\Spi\UnitOfWork $unitOfWork = null): InMemoryServer
    {
        $factory = new Psr17Factory();
        $counter = 0;
        $server = new InMemoryServer(
            new ServerConfig(self::BASE, new Iri(self::HOLDER), $basePath, bulkLogisticsEvents: $bulkEvents, dataHolderType: Cargo::Company),
            new HeaderAuthenticator(),
            $this->clock,
            $this->dispatcher,
            $factory,
            $factory,
            denial: $denial,
            ids: new UuidIdGenerator(static function (int $n) use (&$counter): string {
                return str_pad((string) ++$counter, $n, "\0", STR_PAD_LEFT);
            }),
            unitOfWork: $unitOfWork,
        );
        $server->policy->addInternal(new Iri(self::HOLDER));

        return $server;
    }

    /**
     * @param array<string, string> $headers
     */
    protected function request(string $method, string $path, ?string $agent = self::PARTNER, array $headers = [], ?string $body = null): ResponseInterface
    {
        $defaults = ['Accept' => 'application/ld+json; version=2.3.0'];
        if ($agent !== null) {
            $defaults['X-Test-Agent'] = $agent;
        }
        if ($body !== null) {
            $defaults['Content-Type'] = 'application/ld+json; version=2.3.0';
        }
        $request = new ServerRequest($method, self::BASE . $path, [...$defaults, ...$headers], $body);

        return $this->server->handler->handle($request);
    }

    /**
     * @return array<string, mixed>
     */
    protected static function json(ResponseInterface $response): array
    {
        $decoded = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    protected function piece(string $id = 'piece-1', ?float $weight = 20.0): LogisticsObject
    {
        $builder = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Books')->set(Cargo::coload, false);
        if ($weight !== null) {
            $builder->set(Cargo::grossWeight, Values::quantity($weight, MeasurementUnitCode::KGM));
        }

        return $builder->build(new Iri(self::BASE . '/logistics-objects/' . $id));
    }

    protected function storePiece(string $id = 'piece-1', ?string $readableBy = self::PARTNER): LogisticsObject
    {
        $piece = $this->piece($id)->withEmbeddedIds($this->server->services->embeddedIds);
        $this->server->objects->create($piece, $this->clock->now());
        if ($readableBy !== null) {
            $this->server->policy->allow(new Iri($readableBy), $piece->iri, [Permission::GetLogisticsObject]);
        }

        return $piece;
    }

    /**
     * Narrows a decoded JSON value to an array for further inspection.
     *
     * @return array<array-key, mixed>
     */
    protected static function arr(mixed $value, string $message = ''): array
    {
        self::assertIsArray($value, $message);

        return $value;
    }

    protected static function str(mixed $value, string $message = ''): string
    {
        self::assertIsString($value, $message);

        return $value;
    }

    protected static function assertError(ResponseInterface $response, int $status, string $titleContains = ''): void
    {
        self::assertSame($status, $response->getStatusCode(), (string) $response->getBody());
        $body = self::json($response);
        self::assertSame('api:Error', $body['@type']);
        self::assertIsString($body['api:hasTitle']);
        self::assertStringContainsString($titleContains, $body['api:hasTitle']);
        self::assertStringStartsWith('application/ld+json; version=', $response->getHeaderLine('Content-Type'));
        self::assertSame('en-US', $response->getHeaderLine('Content-Language'));
        self::assertSame('https://onerecord.iata.org/ns/api#Error', $response->getHeaderLine('Type'));
    }
}
