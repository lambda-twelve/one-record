<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class ServerInformationAndNegotiationTest extends ServerTestCase
{
    public function testServerInformation(): void
    {
        $response = $this->request('GET', '/');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/ld+json; version=2.3.0', $response->getHeaderLine('Content-Type'));
        self::assertSame('en-US', $response->getHeaderLine('Content-Language'));
        self::assertSame('https://onerecord.iata.org/ns/api#ServerInformation', $response->getHeaderLine('Type'));
        self::assertSame('Fri, 02 Oct 2026 12:00:00 GMT', $response->getHeaderLine('Last-Modified'));
        $body = self::json($response);
        self::assertSame('api:ServerInformation', $body['@type']);
        self::assertSame(self::BASE, $body['@id']);
        self::assertSame(['@type' => 'cargo:Company', '@id' => self::HOLDER], $body['api:hasDataHolder']);
        self::assertSame(self::BASE, $body['api:hasServerEndpoint']);
        self::assertSame(['2.3.0', '2.2.0'], $body['api:hasSupportedApiVersion']);
        self::assertSame(['application/ld+json'], $body['api:hasSupportedContentType']);
        self::assertSame(['en-US'], $body['api:hasSupportedLanguage']);
        self::assertSame(['https://onerecord.iata.org/ns/cargo', 'https://onerecord.iata.org/ns/api'], $body['api:hasSupportedOntology']);
        $versions = self::arr($body['api:hasSupportedOntologyVersion']);
        self::assertContains('https://onerecord.iata.org/ns/cargo/3.3', $versions);
        self::assertContains('https://onerecord.iata.org/ns/cargo/3.2', $versions);
    }

    public function testHeadReturnsHeadersOnly(): void
    {
        $response = $this->request('HEAD', '/');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('', (string) $response->getBody());
        self::assertGreaterThan(100, (int) $response->getHeaderLine('Content-Length'));
        self::assertSame('https://onerecord.iata.org/ns/api#ServerInformation', $response->getHeaderLine('Type'));
    }

    public function testRequiresAuthentication(): void
    {
        $response = $this->request('GET', '/', agent: null);

        self::assertError($response, 401, 'Not authenticated');
        self::assertSame('Bearer', $response->getHeaderLine('WWW-Authenticate'));
    }

    public function testVersionNegotiation(): void
    {
        self::assertSame('application/ld+json; version=2.2.0', $this->request('GET', '/', headers: ['Accept' => 'application/ld+json; version=2.2.0'])->getHeaderLine('Content-Type'));
        self::assertSame('application/ld+json; version=2.3.0', $this->request('GET', '/', headers: ['Accept' => 'application/ld+json'])->getHeaderLine('Content-Type'), 'no version: the highest');
        self::assertSame('application/ld+json; version=2.3.0', $this->request('GET', '/', headers: ['Accept' => '*/*'])->getHeaderLine('Content-Type'));
        self::assertSame('application/ld+json; version=2.2.0', $this->request('GET', '/', headers: ['Accept' => 'text/turtle;q=0.9, application/ld+json;version=2.2.0'])->getHeaderLine('Content-Type'));
        self::assertSame(200, $this->request('GET', '/', headers: ['Accept' => 'application/ld+json; version=2.2'])->getStatusCode(), 'two-component version accepted');

        self::assertError($this->request('GET', '/', headers: ['Accept' => 'text/turtle']), 406, 'Not acceptable');
        self::assertError($this->request('GET', '/', headers: ['Accept' => 'application/ld+json; version=9.9.0']), 406, 'Not acceptable');
        self::assertError($this->request('GET', '/', headers: ['Accept' => 'application/ld+json; version=1.2']), 406);
        $this->server = $this->makeServerWithVersions();
        self::assertError($this->request('GET', '/', headers: ['Accept' => 'application/ld+json; version=2.3.0']), 406, 'Not acceptable');
    }

    private function makeServerWithVersions(): \LambdaTwelve\OneRecord\Server\InMemory\InMemoryServer
    {
        $factory = new \Nyholm\Psr7\Factory\Psr17Factory();

        return new \LambdaTwelve\OneRecord\Server\InMemory\InMemoryServer(
            new \LambdaTwelve\OneRecord\Server\ServerConfig(self::BASE, new \LambdaTwelve\OneRecord\Rdf\Iri(self::HOLDER), apiVersions: [\LambdaTwelve\OneRecord\Spec\ApiVersion::V2_2_0]),
            new \LambdaTwelve\OneRecord\Testing\HeaderAuthenticator(),
            $this->clock,
            $this->dispatcher,
            $factory,
            $factory,
        );
    }

    public function testLanguageNegotiation(): void
    {
        self::assertSame('en-US', $this->request('GET', '/', headers: ['Accept-Language' => 'de-DE, en;q=0.8'])->getHeaderLine('Content-Language'), 'en-US is the only language and matches "en"');
        self::assertSame('en-US', $this->request('GET', '/', headers: ['Accept-Language' => 'fr'])->getHeaderLine('Content-Language'), 'falls back to en-US');
    }

    public function testUnknownPathsAndMethods(): void
    {
        self::assertError($this->request('GET', '/nothing-here'), 404, 'Resource not found');
        $response = $this->request('DELETE', '/');
        self::assertError($response, 405, 'Method not allowed');
        self::assertSame('GET, HEAD', $response->getHeaderLine('Allow'));
        self::assertError($this->request('GET', '/nothing', agent: null), 404, 'Resource not found');
    }

    public function testBasePathIsHonoured(): void
    {
        $this->server = $this->makeServer(basePath: '/onerecord');
        $response = $this->server->handler->handle(new \Nyholm\Psr7\ServerRequest('GET', self::BASE . '/onerecord/', ['X-Test-Agent' => self::PARTNER, 'Accept' => 'application/ld+json']));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(self::BASE . '/onerecord', self::json($response)['api:hasServerEndpoint']);
        self::assertSame(404, $this->server->handler->handle(new \Nyholm\Psr7\ServerRequest('GET', self::BASE . '/', ['X-Test-Agent' => self::PARTNER]))->getStatusCode());
    }

    public function testErrorsCarryTheSpecShape(): void
    {
        $body = self::json($this->request('GET', '/logistics-objects/missing'));

        self::assertSame(['@id' => 'api:ERROR'], $body['api:hasSeverity'], '2.3 carries the severity');
        $detail = self::arr(self::arr($body['api:hasErrorDetail'])[0]);
        self::assertSame('404', $detail['api:hasCode']);
        self::assertSame(self::BASE . '/logistics-objects/missing', $detail['api:hasResource']);
        self::assertSame('en-US', self::arr($body['@context'])['@language']);

        $old = self::json($this->request('GET', '/logistics-objects/missing', headers: ['Accept' => 'application/ld+json; version=2.2.0']));
        self::assertArrayNotHasKey('api:hasSeverity', $old);
    }

    public function testDecisionHideAnswers404ForForbiddenObjects(): void
    {
        $this->server = $this->makeServer(Decision::Hide);
        $this->storePiece(readableBy: null);

        self::assertError($this->request('GET', '/logistics-objects/piece-1'), 404, 'Resource not found');
        self::assertSame(Cargo::Piece, $this->server->objects->latest($this->piece()->iri)?->object->mostSpecificType());
    }
}
