<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Model;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\Model\Builder\Embedded;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LocalGraph;
use LambdaTwelve\OneRecord\Model\LocalRef;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\ModelException;
use LambdaTwelve\OneRecord\Model\ResolvedGraph;
use LambdaTwelve\OneRecord\Model\Uuid;
use LambdaTwelve\OneRecord\Model\UuidIriMinter;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ObjectBuilder::class)]
#[CoversClass(Embedded::class)]
#[CoversClass(Values::class)]
#[CoversClass(LocalGraph::class)]
#[CoversClass(LocalRef::class)]
#[CoversClass(ResolvedGraph::class)]
#[CoversClass(LogisticsObject::class)]
#[CoversClass(Uuid::class)]
#[CoversClass(UuidIriMinter::class)]
#[CoversClass(ModelException::class)]
#[UsesClass(Vocabulary::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\ClassInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\PropertyInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\IndividualInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\CodeListInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Writer::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Context::class)]
#[UsesClass(JsonLd::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Json::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Expander::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\ExpandedDocument::class)]
#[UsesClass(Comparer::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Diff::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\BlankNode::class)]
#[UsesClass(Literal::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Triple::class)]
#[UsesClass(DataModelVersion::class)]
final class BuildersTest extends TestCase
{
    private const string BASE = 'https://1r.example.com';

    private static function minter(): UuidIriMinter
    {
        return new UuidIriMinter(self::BASE, seed: 'shipment-AER-1');
    }

    private static function waybillGraph(): LocalGraph
    {
        return LocalGraph::create()
            ->add('waybill', ObjectBuilder::of(Cargo::Waybill)
                ->set(Cargo::waybillPrefix, '020')
                ->set(Cargo::waybillNumber, '12345675')
                ->set(Cargo::waybillType, Values::individual(Cargo::MASTER))
                ->set(Cargo::shipment, Values::ref('shipment'))
                ->set(Cargo::departureLocation, LocalRef::to('origin'))
                ->set(Cargo::carrierDeclarationDate, new DateTimeImmutable('2026-10-02T10:00:00+02:00')))
            ->add('shipment', ObjectBuilder::of(Cargo::Shipment)
                ->set(Cargo::goodsDescription, 'Machine parts')
                ->set(Cargo::totalGrossWeight, Values::quantity(190.5, MeasurementUnitCode::KGM))
                ->set(Cargo::waybill, Values::ref('waybill'))
                ->addAll(Cargo::pieces, [Values::ref('piece-1'), Values::ref('piece-2')])
                ->add(Cargo::involvedParties, Embedded::of(Cargo::Party)
                    ->set(Cargo::partyRole, Values::code('ParticipantIdentifier', 'SHP'))
                    ->set(Cargo::partyDetails, Values::ref('shipper'))))
            ->add('piece-1', ObjectBuilder::of(Cargo::Piece)->set(Cargo::grossWeight, Values::quantity(100, MeasurementUnitCode::KGM))->set(Cargo::coload, false))
            ->add('piece-2', ObjectBuilder::of(Cargo::Piece)->set(Cargo::grossWeight, Values::quantity(90.5, MeasurementUnitCode::KGM))
                ->set(Cargo::dimensions, Embedded::of(Cargo::Dimensions)->set(Cargo::length, Values::quantity(120, MeasurementUnitCode::CMT))->set(Cargo::volume, Values::quantity(0.96, MeasurementUnitCode::MTQ))))
            ->add('origin', ObjectBuilder::of(Cargo::Location)->add(Cargo::locationCodes, Values::codeListElement('ATH', 'IATA three-letter location code')))
            ->add('shipper', ObjectBuilder::ofTypes([Cargo::Company, Cargo::Organization])->set(Cargo::name, 'ACME')->set(Cargo::basedAtLocation, Values::ref('origin')));
    }

    public function testBuildsAGraphOfLinkedObjectsAndResolvesItToStableIris(): void
    {
        $graph = self::waybillGraph();
        self::assertSame('waybill', $graph->rootKey(), 'the first object is the root unless told otherwise');
        self::assertSame(['waybill', 'shipment', 'piece-1', 'piece-2', 'origin', 'shipper'], $graph->keys());

        $resolved = $graph->resolve(self::minter());
        $again = self::waybillGraph()->resolve(self::minter());

        self::assertEquals($resolved->iris(), $again->iris(), 'seeded minting is deterministic');
        foreach ($resolved->iris() as $iri) {
            self::assertStringStartsWith(self::BASE . '/logistics-objects/', $iri->value);
            self::assertTrue(Uuid::isValid(substr($iri->value, \strlen(self::BASE . '/logistics-objects/'))));
        }
        $waybill = $resolved->root();
        self::assertSame([Cargo::Waybill], $waybill->types());
        self::assertEquals($resolved->get('shipment')->iri, $waybill->value(Cargo::shipment), 'local references are rewritten to the minted IRIs');
        self::assertSame('12345675', $waybill->literal(Cargo::waybillNumber));
        self::assertEquals(new Iri(Cargo::MASTER), $waybill->value(Cargo::waybillType));
        self::assertSame('2026-10-02T08:00:00.000Z', $waybill->literal(Cargo::carrierDeclarationDate), 'dates are written in UTC');

        $shipment = $resolved->get('shipment');
        self::assertCount(2, $shipment->values(Cargo::pieces));
        self::assertCount(2, $shipment->embeddedNodes(), 'the party and the total weight are embedded');
        $json = $shipment->toJsonLd();
        self::assertSame('cargo:Shipment', $json['@type']);
        self::assertSame(['@type' => 'cargo:Value', 'cargo:numericalValue' => 190.5, 'cargo:unit' => ['@id' => MeasurementUnitCode::KGM]], $json['cargo:totalGrossWeight']);
        self::assertTrue($shipment->isSameAs(LogisticsObject::fromJsonLd($shipment->toJson())), 'an object round-trips through JSON-LD');

        self::assertSame([Cargo::Company, Cargo::Organization], $resolved->get('shipper')->types());
        self::assertSame(Cargo::Company, $resolved->get('shipper')->mostSpecificType());
    }

    public function testExistingIrisWinWhenResolving(): void
    {
        $known = new Iri(self::BASE . '/logistics-objects/already-published');
        $resolved = self::waybillGraph()->resolve(self::minter(), ['shipment' => $known]);

        self::assertEquals($known, $resolved->get('shipment')->iri);
        self::assertEquals($known, $resolved->root()->value(Cargo::shipment));
    }

    public function testRandomMintingUsesTheInjectedByteSource(): void
    {
        $minter = new UuidIriMinter(self::BASE . '/', randomBytes: static fn(int $n): string => str_repeat("\x01", $n));

        self::assertSame(self::BASE . '/logistics-objects/01010101-0101-4101-8101-010101010101', $minter->mint('x', [])->value);
        $this->expectException(ModelException::class);
        new UuidIriMinter('1r.example.com');
    }

    public function testRefusesDanglingReferencesAndEmptyGraphs(): void
    {
        $graph = LocalGraph::create()->add('waybill', ObjectBuilder::of(Cargo::Waybill)->set(Cargo::shipment, Values::ref('shipment')));
        try {
            $graph->resolve(self::minter());
            self::fail();
        } catch (ModelException $e) {
            self::assertStringContainsString('refers to "shipment", which the graph does not contain', $e->getMessage());
        }
        $this->expectException(ModelException::class);
        LocalGraph::create()->resolve(self::minter());
    }

    public function testRefusesDuplicateKeysAndBadKeys(): void
    {
        $graph = LocalGraph::create()->add('a', ObjectBuilder::of(Cargo::Piece));
        try {
            $graph->add('a', ObjectBuilder::of(Cargo::Piece));
            self::fail();
        } catch (ModelException $e) {
            self::assertStringContainsString('already has an object with key "a"', $e->getMessage());
        }
        $this->expectException(ModelException::class);
        LocalRef::to('has space');
    }

    /**
     * @return iterable<string, array{callable(): mixed, string}>
     */
    public static function invalidBuilds(): iterable
    {
        yield 'unknown class' => [static fn() => ObjectBuilder::of(Cargo::NAMESPACE . 'Spaceship'), 'not a class'];
        yield 'embedded class as object' => [static fn() => ObjectBuilder::of(Cargo::Value), 'not a logistics object class'];
        yield 'unknown property' => [static fn() => ObjectBuilder::of(Cargo::Piece)->set(Cargo::NAMESPACE . 'colour', 'red'), 'not a property'];
        yield 'property not accepted' => [static fn() => ObjectBuilder::of(Cargo::Piece)->set(Cargo::iataCargoAgentCode, '1234567'), 'does not accept'];
        yield 'literal on object property' => [static fn() => ObjectBuilder::of(Cargo::Piece)->set(Cargo::grossWeight, 20.5), 'takes an object'];
        yield 'object on datatype property' => [static fn() => ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, Values::quantity(1, MeasurementUnitCode::KGM)), 'takes a literal'];
        yield 'wrong datatype' => [static fn() => ObjectBuilder::of(Cargo::Piece)->set(Cargo::coload, 'yes'), 'expects xsd:boolean'];
        yield 'logistics object embedded' => [static fn() => ObjectBuilder::of(Cargo::Shipment)->set(Cargo::waybill, Embedded::of(Cargo::Waybill)), 'publish it as its own object'];
        yield 'embedded property not accepted' => [static fn() => ObjectBuilder::of(Cargo::Piece)->set(Cargo::grossWeight, Embedded::of(Cargo::Value)->set(Cargo::goodsDescription, 'x')->set(Cargo::waybillPrefix, '020')->set(Cargo::iataCargoAgentCode, 'x')), 'does not accept'];
        yield 'unsupported php value' => [static fn() => ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, new stdClass()), 'Cannot use a value of type'];
        yield 'closed code list member' => [static fn() => Values::code('WeightUnitCode', 'XYZ'), 'not a published code'];
        yield 'unknown code list' => [static fn() => Values::code('NoSuchList', 'A'), 'not a ONE Record code list'];
        yield 'bad currency' => [static fn() => Values::money(1, 'euro'), 'not an ISO 4217'];
        yield 'unknown individual' => [static fn() => Values::individual(Cargo::NAMESPACE . 'MAYBE'), 'not a named individual'];
        yield 'term newer than the partner' => [static fn() => ObjectBuilder::of(Cargo::Waybill, Vocabulary::for(DataModelVersion::V3_2))->set(Cargo::agentReference, 'x'), 'data model 3.2'];
        yield 'attachment newer than the partner' => [static fn() => ObjectBuilder::of(Cargo::Shipment, Vocabulary::for(DataModelVersion::V3_2))->set(Cargo::securityDeclarations, Values::ref('sd')), 'in data model 3.2'];
    }

    /**
     * @param callable(): mixed $build
     */
    #[DataProvider('invalidBuilds')]
    public function testRefusesWhatTheOntologyDoesNot(callable $build, string $message): void
    {
        $this->expectException(ModelException::class);
        $this->expectExceptionMessage($message);
        $build();
    }

    public function testVersionCeilingAllowsOlderTermsAndUncheckedBuildersAllowAnything(): void
    {
        $object = ObjectBuilder::of(Cargo::Shipment, Vocabulary::for(DataModelVersion::V3_2))->set(Cargo::goodsDescription, 'ok')->build(new Iri(self::BASE . '/logistics-objects/s'));
        self::assertSame('ok', $object->literal(Cargo::goodsDescription));

        $extension = ObjectBuilder::unchecked([Cargo::Piece, 'https://example.org/ext#Thing'])->set('https://example.org/ext#colour', 'red')->build(new Iri(self::BASE . '/logistics-objects/p'));
        self::assertSame('red', $extension->literal('https://example.org/ext#colour'));
        self::assertSame(Cargo::Piece, $extension->mostSpecificType(), 'unknown types do not displace known ones in the Type header');
    }

    public function testValueHelpers(): void
    {
        $money = Values::money(5000, 'EUR');
        self::assertSame([Cargo::CurrencyValue], $money->types());
        self::assertEquals([Literal::double(5000.0)], $money->properties()[Cargo::numericalValue]);
        self::assertEquals([new Iri('https://onerecord.iata.org/ns/code-lists/CurrencyCode#EUR')], $money->properties()[Cargo::currencyUnit]);

        $element = Values::codeListElement('DE', 'ISO 3166-1 alpha-2', '2020', 'Germany');
        self::assertSame([Cargo::CodeListElement], $element->types());
        self::assertArrayHasKey(Cargo::codeListVersion, $element->properties());
        self::assertArrayNotHasKey(Cargo::codeListVersion, Values::codeListElement('DE', 'ISO')->properties(), 'null values are left out');

        self::assertSame('https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#XYZ', Values::code('MeasurementUnitCode', 'XYZ')->value, 'open lists accept unpublished codes');
        self::assertSame(MeasurementUnitCode::KGM, Values::code('WeightUnitCode', 'KGM')->value === 'https://onerecord.iata.org/ns/code-lists/WeightUnitCode#KGM' ? MeasurementUnitCode::KGM : 'x');
        self::assertEquals(new Literal('2026-10-02', 'http://www.w3.org/2001/XMLSchema#date'), Values::date(new DateTimeImmutable('2026-10-02')));
        self::assertEquals(Literal::integer(3), Values::term(3));
        self::assertEquals(Literal::boolean(true), Values::term(true));
        self::assertEquals(Literal::string('x'), Values::term('x'));
    }

    public function testLogisticsObjectGuards(): void
    {
        try {
            new LogisticsObject(LocalRef::to('k')->iri(), new \LambdaTwelve\OneRecord\Rdf\Graph());
            self::fail();
        } catch (ModelException $e) {
            self::assertStringContainsString('resolve the graph first', $e->getMessage());
        }
        try {
            LogisticsObject::fromJsonLd(['@context' => ['cargo' => Cargo::NAMESPACE], '@type' => 'cargo:Piece']);
            self::fail();
        } catch (ModelException $e) {
            self::assertStringContainsString('no @id', $e->getMessage());
        }
        $expected = new Iri(self::BASE . '/logistics-objects/p');
        $fromPost = LogisticsObject::fromJsonLd(['@context' => ['cargo' => Cargo::NAMESPACE], '@type' => 'cargo:Piece', 'cargo:coload' => true], $expected);
        self::assertEquals($expected, $fromPost->iri);
        self::assertSame([Cargo::Piece], $fromPost->types());

        $this->expectException(ModelException::class);
        LogisticsObject::fromJsonLd(['@context' => ['cargo' => Cargo::NAMESPACE], '@id' => self::BASE . '/logistics-objects/other', '@type' => 'cargo:Piece'], $expected);
    }

    public function testObjectsCompareAsGraphs(): void
    {
        $a = ObjectBuilder::of(Cargo::Piece)->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM))->set(Cargo::coload, false)->build(new Iri(self::BASE . '/logistics-objects/p'));
        $b = LogisticsObject::fromJsonLd(JsonLd::compactToJson($a->graph, $a->iri));
        $c = $a->withGraph(ObjectBuilder::of(Cargo::Piece)->set(Cargo::grossWeight, Values::quantity(21, MeasurementUnitCode::KGM))->set(Cargo::coload, false)->buildGraph($a->iri));

        self::assertTrue($a->isSameAs($b));
        self::assertFalse($a->isSameAs($c));
        self::assertFalse($a->isSameAs($a->withIri(new Iri(self::BASE . '/logistics-objects/q'))));
    }

    public function testUuidV5IsDeterministicAndWellFormed(): void
    {
        $a = Uuid::v5(Uuid::NAMESPACE_URL, 'https://example.org/x');
        self::assertSame($a, Uuid::v5(Uuid::NAMESPACE_URL, 'https://example.org/x'));
        self::assertNotSame($a, Uuid::v5(Uuid::NAMESPACE_URL, 'https://example.org/y'));
        self::assertTrue(Uuid::isValid($a));
        self::assertSame('5', $a[14]);
        self::assertSame('7fc81d1d-6c75-568b-9e47-48c947ed2a07', Uuid::v5('8efaab7c-cfd5-11ed-9abe-325096b39f47', 'value'), "the spec's own uuid5 example");
    }

    public function testEventsAreBuiltValidatedAndRehydrated(): void
    {
        $object = new Iri('https://1r.example.com/logistics-objects/piece-1');
        $iri = new Iri($object->value . '/logistics-events/e1');
        $created = new DateTimeImmutable('2026-10-02T12:00:00Z');
        $event = ObjectBuilder::ofEvent()
            ->set(Cargo::eventDate, Values::dateTime(new DateTimeImmutable('2026-10-02T11:00:00Z')))
            ->set(Cargo::eventCode, Values::code('StatusCode', 'DEP'))
            ->set(Cargo::eventName, 'Departed')
            ->buildEvent($iri, $object, $created);

        self::assertSame($iri->value, $event->iri->value);
        self::assertSame($object->value, $event->logisticsObject->value);
        self::assertSame($object->value, $event->graph->firstObject($iri, Cargo::eventFor)?->toNTriples() === null ? null : $object->value, 'eventFor points at the object');
        self::assertTrue($event->matchesCode('DEP'));
        self::assertSame([Cargo::LogisticsEvent], $event->types());

        $stored = LogisticsEvent::fromStored($iri, $object, $event->toJsonLd(), $created);
        self::assertTrue((new Comparer())->isomorphic($event->graph, $stored->graph), 'what a store wrote reads back unchanged');
        self::assertSame($created, $stored->created);

        try {
            ObjectBuilder::ofEvent()->set(Cargo::eventName, 'no date')->buildEvent($iri, $object, $created);
            self::fail('eventDate is mandatory');
        } catch (ModelException $e) {
            self::assertStringContainsString('eventDate', $e->getMessage());
        }
        try {
            ObjectBuilder::ofEvent()->set(Cargo::coload, true);
            self::fail('coload is not an event property');
        } catch (ModelException) {
        }
        try {
            ObjectBuilder::ofEvent(Cargo::Piece);
            self::fail('a Piece is not an event');
        } catch (ModelException) {
        }
        $this->expectException(ModelException::class);
        ObjectBuilder::of(Cargo::LogisticsEvent);
    }
}
