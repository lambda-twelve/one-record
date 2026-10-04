<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ServerConfig::class)]
final class ServerConfigTest extends TestCase
{
    public function testProblemsAreListedWithoutConstructing(): void
    {
        self::assertSame(['The base URL is not set.', 'The data holder is not set: the IRI of the organisation this server speaks for.'], ServerConfig::problems([]), 'a fresh install, in the SDK\'s own words (laravel3.md, item 3)');
        self::assertSame([], ServerConfig::problems(['baseUrl' => 'https://1r.example.com', 'dataHolder' => 'https://1r.example.com/logistics-objects/holder']));
        self::assertSame([], ServerConfig::problems(['baseUrl' => 'https://1r.example.com', 'dataHolder' => new Iri('https://1r.example.com/logistics-objects/holder'), 'apiVersions' => ['2.3.0', ApiVersion::V2_2_0], 'dataModelVersions' => ['3.3'], 'basePath' => '/one-record', 'languages' => ['en-US', 'de-DE'], 'maxBodyBytes' => 10, 'embeddedDepth' => 0]), 'raw strings and typed values alike');

        $problems = ServerConfig::problems(['baseUrl' => 'https://1r.example.com/path', 'dataHolder' => 'not an iri', 'basePath' => 'one-record/', 'apiVersions' => ['9.9.9'], 'dataModelVersions' => [], 'languages' => ['de-DE'], 'maxBodyBytes' => 0, 'embeddedDepth' => -1]);
        self::assertCount(8, $problems);
        self::assertStringContainsString('scheme and host only', $problems[0]);
        self::assertStringContainsString('not a valid IRI', $problems[1]);
        self::assertStringContainsString('base path', $problems[2]);
        self::assertStringContainsString('Unknown API version "9.9.9"', $problems[3]);
        self::assertStringContainsString('data model version', $problems[4]);
        self::assertStringContainsString('en-US', $problems[5]);
        self::assertStringContainsString('body limit', $problems[6]);
        self::assertStringContainsString('embedding depth', $problems[7]);
    }

    public function testTheConstructorRefusesWhatProblemsReports(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The base path must start with "/" and not end with one.');
        new ServerConfig('https://1r.example.com', new Iri('https://1r.example.com/logistics-objects/holder'), 'one-record');
    }
}
