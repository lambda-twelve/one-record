<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Vocabulary;

use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Spec\Edition;
use LambdaTwelve\OneRecord\Vocabulary\ClassInfo;
use LambdaTwelve\OneRecord\Vocabulary\CodeListInfo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\WeightUnitCode;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Manifest;
use LambdaTwelve\OneRecord\Vocabulary\IndividualInfo;
use LambdaTwelve\OneRecord\Vocabulary\PropertyInfo;
use LambdaTwelve\OneRecord\Vocabulary\PropertyKind;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Runs against the committed generated vocabulary, so it doubles as a check
 * that the real IATA ontologies were read correctly.
 */
#[CoversClass(Vocabulary::class)]
#[CoversClass(ClassInfo::class)]
#[CoversClass(PropertyInfo::class)]
#[CoversClass(IndividualInfo::class)]
#[CoversClass(CodeListInfo::class)]
#[CoversClass(PropertyKind::class)]
#[UsesClass(Edition::class)]
#[UsesClass(DataModelVersion::class)]
final class VocabularyTest extends TestCase
{
    public function testKnowsTheSizeOfTheRealOntologies(): void
    {
        $vocabulary = Vocabulary::default();

        $cargoClasses = array_filter($vocabulary->classes(), static fn(ClassInfo $c): bool => str_starts_with($c->iri, Cargo::NAMESPACE));
        $apiClasses = array_filter($vocabulary->classes(), static fn(ClassInfo $c): bool => str_starts_with($c->iri, Api::NAMESPACE));
        self::assertCount(115, $cargoClasses, '114 classes in 3.2 plus StatusUpdateEvent in 3.3');
        self::assertCount(27, $apiClasses, '23 classes in API 2.2.0 plus four in 2.3.0');
        self::assertCount(564 + 64, $vocabulary->properties());
        self::assertCount(46, $vocabulary->codeLists());
        self::assertSame(1286, array_sum(array_map(static fn(CodeListInfo $l): int => \count($l->codes), $vocabulary->codeLists())));

        self::assertSame(array_map(static fn(Edition $e): string => $e->value, Edition::cases()), array_keys(Manifest::EDITIONS));
        self::assertSame(Edition::latest()->value, Manifest::LATEST_EDITION);
        foreach (Manifest::EDITIONS as $tag => $edition) {
            self::assertStringStartsWith(Edition::from($tag)->dataModelVersion()->value, $edition['versionInfo']['cargo'], "ontology read for {$tag} is the final one, not a release candidate");
            self::assertSame(Edition::from($tag)->apiVersion()->value, $edition['versionInfo']['api']);
        }
    }

    public function testClassHierarchyAndLogisticsObjects(): void
    {
        $v = Vocabulary::default();

        self::assertSame([Cargo::PhysicalLogisticsObject, Cargo::LogisticsObject], $v->ancestors(Cargo::Piece));
        self::assertTrue($v->isLogisticsObjectClass(Cargo::Piece));
        self::assertTrue($v->isLogisticsObjectClass(Cargo::Company));
        self::assertFalse($v->isLogisticsObjectClass(Cargo::Value));
        self::assertFalse($v->isLogisticsObjectClass(Cargo::LogisticsEvent), 'events are neither logistics objects nor embedded objects');
        self::assertFalse($v->isLogisticsObjectClass(Api::Change));
        self::assertTrue($v->isClass(Api::ServerInformation));
        self::assertFalse($v->isClass(Cargo::NAMESPACE . 'Spaceship'));

        self::assertSame([Cargo::Company], $v->mostSpecific([Cargo::Company, Cargo::Organization, Cargo::LogisticsAgent, Cargo::LogisticsObject]));
        self::assertSame([Cargo::Piece, Cargo::Shipment], $v->mostSpecific([Cargo::Piece, Cargo::LogisticsObject, Cargo::Shipment]));
        self::assertSame(['x:Unknown', Cargo::Piece], $v->mostSpecific(['x:Unknown', Cargo::Piece]), 'unknown types are kept');
    }

    public function testPropertiesAcceptedByAClass(): void
    {
        $v = Vocabulary::default();

        self::assertTrue($v->accepts([Cargo::Piece], Cargo::grossWeight));
        self::assertTrue($v->accepts([Cargo::Piece], Cargo::goodsDescription), 'declared for any class');
        self::assertTrue($v->accepts([Cargo::Piece], Cargo::events), 'inherited from LogisticsObject');
        self::assertFalse($v->accepts([Cargo::Piece], Cargo::iataCargoAgentCode), 'declared for Company only');
        self::assertTrue($v->accepts([Cargo::Piece], Cargo::waybillNumber), 'IATA declares waybillNumber for owl:Thing, so any class may carry it');
        self::assertFalse($v->accepts([Cargo::Value], Cargo::grossWeight));
        self::assertTrue($v->accepts([Cargo::Value, Cargo::Piece], Cargo::grossWeight), 'any of the types may carry it');

        $grossWeight = $v->property(Cargo::grossWeight);
        self::assertNotNull($grossWeight);
        self::assertSame(PropertyKind::Object, $grossWeight->kind);
        self::assertSame([Cargo::Value], $grossWeight->ranges);
        self::assertFalse($grossWeight->isDeprecated());

        $accepted = $v->propertiesOf(Cargo::Piece);
        self::assertArrayHasKey(Cargo::grossWeight, $accepted);
        self::assertArrayHasKey(Cargo::goodsDescription, $accepted);
        self::assertArrayHasKey(Cargo::securityDeclarations, $accepted);
        self::assertSame(array_keys($accepted), array_keys($v->propertiesOf(Cargo::Piece)), 'memoised result is stable');
    }

    public function testVersionMetadataAndViews(): void
    {
        $all = Vocabulary::default();
        $v32 = Vocabulary::for(DataModelVersion::V3_2);

        $statusUpdate = $all->class(Cargo::StatusUpdateEvent);
        self::assertNotNull($statusUpdate);
        self::assertSame('3.3', $statusUpdate->since);
        self::assertNull($v32->class(Cargo::StatusUpdateEvent), 'a 3.2 partner does not know StatusUpdateEvent');
        self::assertNull($v32->property(Cargo::agentReference));
        self::assertNotNull($v32->property(Cargo::grossWeight));

        $totalDimensions = $all->property(Cargo::totalDimensions);
        self::assertNotNull($totalDimensions);
        self::assertSame('3.3', $totalDimensions->deprecatedIn);
        self::assertTrue($totalDimensions->isDeprecated());
        self::assertNotNull($v32->property(Cargo::totalDimensions), 'still a valid 3.2 term');

        self::assertSame(DataModelVersion::V3_2, $v32->ceiling());
        self::assertNull($all->ceiling());
        self::assertTrue($all->accepts([Cargo::Shipment], Cargo::securityDeclarations));
        self::assertFalse($v32->accepts([Cargo::Shipment], Cargo::securityDeclarations), 'attached to Shipment only in 3.3; declared for Piece');
        self::assertTrue($v32->accepts([Cargo::Piece], Cargo::securityDeclarations));
        self::assertTrue($v32->accepts([Cargo::Shipment], Cargo::goodsDescription));
        self::assertNotNull($v32->class(Api::Severity), 'API terms are not filtered by data model version');
    }

    public function testCodeLists(): void
    {
        $v = Vocabulary::default();

        self::assertTrue($v->isCodeListMember(MeasurementUnitCode::KGM));
        self::assertTrue($v->isCodeListMember(WeightUnitCode::KGM));
        self::assertFalse($v->isCodeListMember('https://onerecord.iata.org/ns/code-lists/CurrencyCode#EUR'), 'currencies are not published');
        self::assertTrue($v->isOpenCodeListIri('https://onerecord.iata.org/ns/code-lists/CurrencyCode#EUR'));
        self::assertFalse($v->isOpenCodeListIri(WeightUnitCode::LBR));
        self::assertFalse($v->isCodeListMember(Cargo::Piece));
        self::assertFalse($v->isCodeListMember('https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode'));

        $density = $v->codeList('https://onerecord.iata.org/ns/code-lists/DensityGroupCode');
        self::assertNotNull($density);
        self::assertTrue($density->hasCode('10'));
        self::assertSame('https://onerecord.iata.org/ns/code-lists/DensityGroupCode#10', $density->iriOf('10'));
        self::assertSame(['0', '1', '10', '2', '3', '4', '5', '6', '8', '9'], array_map('strval', array_keys($density->codes)));

        $units = $v->codeList(MeasurementUnitCode::IRI);
        self::assertNotNull($units);
        self::assertTrue($units->open);
        self::assertSame(MeasurementUnitCode::OPEN, $units->open);
        self::assertCount(40, MeasurementUnitCode::ALL);
    }

    public function testIndividuals(): void
    {
        $v = Vocabulary::default();

        $actual = $v->individual(Cargo::ACTUAL);
        self::assertNotNull($actual);
        self::assertSame([Cargo::ActionTimeType, Cargo::EventTimeType, Cargo::MovementTimeType], $actual->types);
        $pending = $v->individual(Api::REQUEST_PENDING);
        self::assertNotNull($pending);
        self::assertSame([Api::RequestStatus], $pending->types);
        self::assertNull($v->individual(Cargo::NAMESPACE . 'NOPE'));
    }
}
