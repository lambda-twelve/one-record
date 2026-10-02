<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Spec;

use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Spec\Edition;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Guards that keep "adding a new IATA edition" a mechanical change: every
 * version must belong to an edition, and every edition must have the pieces
 * the rest of the package relies on (fixtures, a spec-coverage section, ...).
 * Later phases extend this test as those pieces appear.
 */
#[CoversClass(Edition::class)]
#[CoversClass(ApiVersion::class)]
#[CoversClass(DataModelVersion::class)]
#[CoversClass(Namespaces::class)]
final class EditionsTest extends TestCase
{
    public function testEveryApiVersionBelongsToExactlyOneEdition(): void
    {
        foreach (ApiVersion::cases() as $version) {
            $editions = array_filter(Edition::cases(), static fn(Edition $e): bool => $e->apiVersion() === $version);
            self::assertCount(1, $editions, "API {$version->value} must be declared by exactly one edition.");
            self::assertSame($version, $version->edition()->apiVersion());
        }
    }

    public function testEveryDataModelVersionBelongsToAnEdition(): void
    {
        foreach (DataModelVersion::cases() as $version) {
            self::assertSame($version, $version->edition()->dataModelVersion());
        }
    }

    public function testEditionsArePinnedToFullCommits(): void
    {
        foreach (Edition::cases() as $edition) {
            self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $edition->ontologyCommit());
            self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $edition->documentationCommit());
            self::assertSame($edition->value . '-standard', $edition->ontologyFolder());
        }
    }

    public function testLatestIsTheHighestVersion(): void
    {
        self::assertSame(ApiVersion::V2_3_0, ApiVersion::latest());
        self::assertSame(DataModelVersion::V3_3, DataModelVersion::latest());
        self::assertSame(Edition::E2026_07, Edition::latest());
        self::assertSame([ApiVersion::V2_3_0, ApiVersion::V2_2_0], ApiVersion::allDescending());
        self::assertTrue(ApiVersion::V2_3_0->isAtLeast(ApiVersion::V2_2_0));
        self::assertFalse(ApiVersion::V2_2_0->isAtLeast(ApiVersion::V2_3_0));
        self::assertTrue(DataModelVersion::V3_3->isAtLeast(DataModelVersion::V3_2));
    }

    /**
     * @return iterable<string, array{string, ApiVersion|null}>
     */
    public static function apiVersionStrings(): iterable
    {
        yield 'exact' => ['2.2.0', ApiVersion::V2_2_0];
        yield 'two components' => ['2.3', ApiVersion::V2_3_0];
        yield 'v prefix' => ['v2.3.0', ApiVersion::V2_3_0];
        yield 'padded' => [' 2.2.0 ', ApiVersion::V2_2_0];
        yield 'unknown' => ['1.2', null];
        yield 'garbage' => ['latest', null];
    }

    #[DataProvider('apiVersionStrings')]
    public function testApiVersionParsesLeniently(string $input, ?ApiVersion $expected): void
    {
        self::assertSame($expected, ApiVersion::tryFromString($input));
    }

    public function testDataModelVersionParsesBothSpellings(): void
    {
        self::assertSame(DataModelVersion::V3_2, DataModelVersion::tryFromString('3.2'));
        self::assertSame(DataModelVersion::V3_2, DataModelVersion::tryFromString('3.2.0'));
        self::assertSame(DataModelVersion::V3_3, DataModelVersion::tryFromString('3.3.0'));
        self::assertNull(DataModelVersion::tryFromString('3.1'));
    }

    public function testOntologyIrisFollowIatasScheme(): void
    {
        self::assertSame('https://onerecord.iata.org/ns/cargo/3.2', DataModelVersion::V3_2->ontologyVersionIri());
        self::assertSame('https://onerecord.iata.org/ns/api/2.3.0', ApiVersion::V2_3_0->ontologyVersionIri());
        self::assertSame('https://onerecord.iata.org/ns/cargo#Piece', Namespaces::cargo('Piece'));
        self::assertSame('https://onerecord.iata.org/ns/api#Change', Namespaces::api('Change'));
        self::assertSame('http://www.w3.org/2001/XMLSchema#dateTime', Namespaces::xsd('dateTime'));
        self::assertSame(
            'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM',
            Namespaces::codeList('MeasurementUnitCode', 'KGM'),
        );
    }
}
