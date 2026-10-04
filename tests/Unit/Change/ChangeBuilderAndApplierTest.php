<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Change;

use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\ChangeApplier;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Change\ChangeException;
use LambdaTwelve\OneRecord\Change\ChangeRejected;
use LambdaTwelve\OneRecord\Change\ChangeResult;
use LambdaTwelve\OneRecord\Change\Operation;
use LambdaTwelve\OneRecord\Change\OperationKind;
use LambdaTwelve\OneRecord\Change\OperationObject;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\Model\Builder\Embedded;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\Uuid5EmbeddedIdMinter;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChangeBuilder::class)]
#[CoversClass(ChangeApplier::class)]
#[CoversClass(ChangeResult::class)]
#[CoversClass(ChangeRejected::class)]
#[CoversClass(Uuid5EmbeddedIdMinter::class)]
#[UsesClass(Change::class)]
#[UsesClass(ChangeException::class)]
#[UsesClass(Operation::class)]
#[UsesClass(OperationObject::class)]
#[UsesClass(OperationKind::class)]
#[UsesClass(Error::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Api\ErrorDetail::class)]
#[UsesClass(Comparer::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Diff::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\JsonLd::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Expander::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\ExpandedDocument::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Context::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Json::class)]
#[UsesClass(ObjectBuilder::class)]
#[UsesClass(Embedded::class)]
#[UsesClass(Values::class)]
#[UsesClass(LogisticsObject::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Model\Uuid::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\Vocabulary::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\ClassInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\PropertyInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\IndividualInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\CodeListInfo::class)]
#[UsesClass(Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(BlankNode::class)]
#[UsesClass(Literal::class)]
#[UsesClass(Triple::class)]
final class ChangeBuilderAndApplierTest extends TestCase
{
    private const string PIECE = 'https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b7c';

    private static function piece(): ObjectBuilder
    {
        return ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'ONE Record Advertisement Materials')->set(Cargo::coload, false);
    }

    private static function stored(ObjectBuilder $builder): LogisticsObject
    {
        // What a server holds: embedded objects already carry internal: ids. Apply a creating change of nothing,
        // so just build and give blank nodes ids through a round trip of the applier's minter semantics.
        $object = $builder->build(new Iri(self::PIECE));
        $graph = new Graph();
        $minter = new Uuid5EmbeddedIdMinter();
        $ids = [];
        foreach ($object->graph as $triple) {
            $s = $triple->subject instanceof BlankNode ? ($ids[$triple->subject->label] ??= $minter->mint($object->iri, 'seed:' . $triple->subject->label)) : $triple->subject;
            $o = $triple->object instanceof BlankNode ? ($ids[$triple->object->label] ??= $minter->mint($object->iri, 'seed:' . $triple->object->label)) : $triple->object;
            $graph->add(new Triple($s, $triple->predicate, $o));
        }

        return new LogisticsObject($object->iri, $graph);
    }

    private static function assertIsomorphic(LogisticsObject $expected, LogisticsObject $actual): void
    {
        $diff = (new Comparer())->compare($expected->graph, $actual->graph);
        self::assertTrue($diff->isEqual(), $diff->describe());
    }

    public function testNoDifferenceYieldsNoChange(): void
    {
        $a = self::stored(self::piece()->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM)));
        $b = self::piece()->set(Cargo::grossWeight, Values::quantity(20.0, MeasurementUnitCode::KGM))->build(new Iri(self::PIECE));

        self::assertNull((new ChangeBuilder())->diff($a, $b, 1), 'blank nodes versus stored ids and 20 versus 20.0 are the same object');
    }

    public function testLiteralChangesBecomeDeleteAndAddPairs(): void
    {
        $from = self::stored(self::piece());
        $to = self::piece()->set(Cargo::goodsDescription, 'BOOKS')->set(Cargo::coload, true)->build(new Iri(self::PIECE));

        $change = (new ChangeBuilder())->diff($from, $to, 1, 'Update goods description and coload');
        self::assertNotNull($change);
        self::assertSame(1, $change->revision);
        $ops = array_map(static fn(Operation $o): string => $o->kind->name . ' ' . $o->predicate->localName() . '=' . $o->object->value . ' (' . $o->object->datatype . ')', $change->operations);
        self::assertSame([
            'Delete coload=false (' . Literal::XSD_BOOLEAN . ')',
            'Add coload=true (' . Literal::XSD_BOOLEAN . ')',
            'Delete goodsDescription=ONE Record Advertisement Materials (' . Literal::XSD_STRING . ')',
            'Add goodsDescription=BOOKS (' . Literal::XSD_STRING . ')',
        ], $ops);

        $result = (new ChangeApplier())->apply($from, 1, $change);
        self::assertIsomorphic($to, $result->object);
        self::assertSame([Cargo::coload, Cargo::goodsDescription], $result->changedProperties);
    }

    public function testAddingAnEmbeddedObjectUsesABlankNodeAsInExampleC2(): void
    {
        $from = self::stored(self::piece());
        $to = self::piece()->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM))->build(new Iri(self::PIECE));

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        self::assertCount(3, $change->operations);
        $link = $change->operations[0];
        self::assertSame(OperationKind::Add, $link->kind);
        self::assertSame(Cargo::grossWeight, $link->predicate->value);
        self::assertSame(Cargo::Value, $link->object->datatype);
        self::assertSame('_:b0', $link->object->value);
        self::assertSame('_:b0', $change->operations[1]->subjectString());

        $result = (new ChangeApplier())->apply($from, 1, $change);
        self::assertIsomorphic($to, $result->object);
        $weight = $result->object->value(Cargo::grossWeight);
        self::assertInstanceOf(Iri::class, $weight);
        self::assertStringStartsWith(Namespaces::EMBEDDED, $weight->value, 'the new embedded object got a stable id');
        self::assertEquals([new Iri(Cargo::Value)], $result->object->graph->typesOf($weight), 'and its class from the datatype');
    }

    public function testChangingInsideAnEmbeddedObjectEditsItInPlaceAsInExampleC3(): void
    {
        $from = self::stored(self::piece()->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM)));
        $to = self::piece()->set(Cargo::grossWeight, Values::quantity(25, MeasurementUnitCode::KGM))->build(new Iri(self::PIECE));
        $weightId = $from->value(Cargo::grossWeight);
        self::assertInstanceOf(Iri::class, $weightId);

        $change = (new ChangeBuilder())->diff($from, $to, 4);
        self::assertNotNull($change);
        self::assertCount(2, $change->operations);
        foreach ($change->operations as $operation) {
            self::assertEquals($weightId, $operation->subject, 'operations address the existing embedded object by its id');
            self::assertSame(Cargo::numericalValue, $operation->predicate->value);
        }
        self::assertSame([], $change->changedProperties(), 'nothing changed on the piece itself');

        $result = (new ChangeApplier())->apply($from, 4, $change);
        self::assertIsomorphic($to, $result->object);
        self::assertEquals($weightId, $result->object->value(Cargo::grossWeight), 'the embedded id is preserved');
    }

    public function testRemovingAnEmbeddedObjectDeletesItsTriplesAsInExampleC4(): void
    {
        $from = self::stored(self::piece()->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM)));
        $to = self::piece()->build(new Iri(self::PIECE));

        $change = (new ChangeBuilder())->diff($from, $to, 2);
        self::assertNotNull($change);
        self::assertCount(3, $change->operations);
        self::assertSame([OperationKind::Delete, OperationKind::Delete, OperationKind::Delete], array_map(static fn(Operation $o): OperationKind => $o->kind, $change->operations));
        self::assertSame(Cargo::Value, $change->operations[0]->object->datatype);

        $result = (new ChangeApplier())->apply($from, 2, $change);
        self::assertIsomorphic($to, $result->object);
        self::assertCount(3, $result->object->graph, 'type, description and coload remain; no orphaned triples');
    }

    public function testMultiValuedEmbeddedObjectsAreMatchedByContent(): void
    {
        $from = self::stored(ObjectBuilder::of(Cargo::Shipment)
            ->add(Cargo::involvedParties, Embedded::of(Cargo::Party)->set(Cargo::partyRole, Values::code('ParticipantIdentifier', 'SHP')))
            ->add(Cargo::involvedParties, Embedded::of(Cargo::Party)->set(Cargo::partyRole, Values::code('ParticipantIdentifier', 'CNE'))));
        $to = ObjectBuilder::of(Cargo::Shipment)
            ->add(Cargo::involvedParties, Embedded::of(Cargo::Party)->set(Cargo::partyRole, Values::code('ParticipantIdentifier', 'CNE')))
            ->add(Cargo::involvedParties, Embedded::of(Cargo::Party)->set(Cargo::partyRole, Values::code('ParticipantIdentifier', 'AGT')))
            ->build(new Iri(self::PIECE));

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        // CNE matches by content and is untouched; the one unmatched old (SHP) and new (AGT)
        // party of the same type are edited in place: delete the old role, add the new one.
        self::assertCount(2, $change->operations);
        self::assertSame([Cargo::partyRole, Cargo::partyRole], array_map(static fn(Operation $o): string => $o->predicate->value, $change->operations));
        self::assertIsomorphic($to, (new ChangeApplier())->apply($from, 1, $change)->object);
    }

    public function testReferencesAndCodeListValuesDiffAsPlainValues(): void
    {
        $other = 'https://1r.example.com/logistics-objects/customs-1';
        $from = self::stored(self::piece()->add(Cargo::specialHandlingCodes, Values::code('SpecialHandlingCode', 'VAL')));
        $to = self::piece()->add(Cargo::specialHandlingCodes, Values::code('SpecialHandlingCode', 'EAP'))->add(Cargo::customsInformation, Values::iri($other))->build(new Iri(self::PIECE));

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        $byPredicate = [];
        foreach ($change->operations as $operation) {
            $byPredicate[$operation->predicate->localName()][] = $operation->kind->name . ':' . $operation->object->value . ':' . $operation->object->datatype;
        }
        self::assertSame(['Add:' . $other . ':' . Cargo::CustomsInformation], $byPredicate['customsInformation'], 'a reference carries the range class as datatype');
        self::assertContains('Delete:https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#VAL:https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode', $byPredicate['specialHandlingCodes']);
        self::assertIsomorphic($to, (new ChangeApplier())->apply($from, 1, $change)->object);
    }

    public function testEventsAndTypesAreNeverPartOfAChange(): void
    {
        $from = self::stored(self::piece());
        $withEvent = ObjectBuilder::unchecked([Cargo::Piece])->set(Cargo::goodsDescription, 'ONE Record Advertisement Materials')->set(Cargo::coload, false)
            ->add(Cargo::events, Values::iri(self::PIECE . '/logistics-events/e1'))->build(new Iri(self::PIECE));
        self::assertNull((new ChangeBuilder())->diff($from, $withEvent, 1), 'cargo:events is ignored by the diff');

        $retyped = ObjectBuilder::of(Cargo::Shipment)->build(new Iri(self::PIECE));
        $this->expectException(ChangeException::class);
        (new ChangeBuilder())->diff($from, $retyped, 1);
    }

    public function testAppliesTheSpecExampleSequence(): void
    {
        $fixtures = __DIR__ . '/../../Fixtures/spec/2026-07/';
        $piece = LogisticsObject::fromJsonLd((string) file_get_contents($fixtures . 'Piece.json'), new Iri(self::PIECE));
        $applier = new ChangeApplier();

        $c1 = Change::fromJsonLd((string) file_get_contents($fixtures . 'Change_example1.json'));
        $r1 = $applier->apply($piece, $c1->revision, $c1);
        self::assertSame('ONE Record Advertisement Materials', $r1->object->literal(Cargo::goodsDescription), 'the 2026-07 example adds this value, whatever its prose says');
        self::assertEquals(Literal::boolean(true), $r1->object->value(Cargo::coload));

        $c2 = Change::fromJsonLd((string) file_get_contents($fixtures . 'Change_example2.json'));
        $r2 = $applier->apply($r1->object, $c2->revision, $c2);
        $weight = $r2->object->value(Cargo::grossWeight);
        self::assertInstanceOf(Iri::class, $weight);
        $added = $r2->object->graph->firstObject($weight, Cargo::numericalValue);
        self::assertInstanceOf(Literal::class, $added);
        self::assertSame('20.0', $added->lexical);

        // Example C3 addresses the embedded object by the id the server assigned; substitute ours.
        $c3Json = str_replace('internal:7fc81d1d-6c75-568b-9e47-48c947ed2a07', $weight->value, (string) file_get_contents($fixtures . 'Change_example3.json'));
        $c3 = Change::fromJsonLd($c3Json);
        $r3 = $applier->apply($r2->object, $c3->revision, $c3);
        $value = $r3->object->graph->firstObject($weight, Cargo::numericalValue);
        self::assertInstanceOf(Literal::class, $value);
        self::assertSame('25.0', $value->lexical);

        $c4Json = str_replace('internal:7fc81d1d-6c75-568b-9e47-48c947ed2a07', $weight->value, (string) file_get_contents($fixtures . 'Change_example4.json'));
        $c4 = Change::fromJsonLd($c4Json);
        try {
            $applier->apply($r3->object, $c4->revision, $c4);
            self::fail('C4 deletes the 20.0 value that C3 replaced; it must fail as a whole');
        } catch (ChangeRejected $e) {
            self::assertSame('422', $e->errors[0]->details[0]->code);
        }
    }

    public function testRejectsWhatCannotBeApplied(): void
    {
        $from = self::stored(self::piece()->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM)));
        $applier = new ChangeApplier();
        $s = new Iri(self::PIECE);
        $literal = static fn(string $v): OperationObject => new OperationObject(Literal::XSD_STRING, $v);

        $cases = [
            ['revision mismatch', new Change($s, 7, [Operation::add($s, new Iri(Cargo::goodsDescription), $literal('x'))]), '409'],
            ['wrong object', new Change(new Iri('https://1r.example.com/logistics-objects/other'), 1, [Operation::add($s, new Iri(Cargo::goodsDescription), $literal('x'))]), '400'],
            ['events', new Change($s, 1, [Operation::add($s, new Iri(Cargo::events), new OperationObject(Cargo::LogisticsEvent, self::PIECE . '/logistics-events/e'))]), '400'],
            ['type of the object', new Change($s, 1, [Operation::add($s, new Iri(Graph::RDF_TYPE), new OperationObject(Cargo::Shipment, Cargo::Shipment))]), '400'],
            ['unknown subject', new Change($s, 1, [Operation::add(new Iri('https://1r.example.com/logistics-objects/other'), new Iri(Cargo::goodsDescription), $literal('x'))]), '400'],
            ['blank subject never introduced', new Change($s, 1, [Operation::add(new BlankNode('b9'), new Iri(Cargo::numericalValue), new OperationObject(Literal::XSD_DOUBLE, '1'))]), '400'],
            ['delete of absent value', new Change($s, 1, [Operation::delete($s, new Iri(Cargo::goodsDescription), $literal('never there'))]), '422'],
            ['duplicate add', new Change($s, 1, [Operation::add($s, new Iri(Cargo::coload), new OperationObject(Literal::XSD_BOOLEAN, 'false'))]), '422'],
            ['property not accepted', new Change($s, 1, [Operation::add($s, new Iri(Cargo::iataCargoAgentCode), $literal('x'))]), '400'],
            ['unknown property', new Change($s, 1, [Operation::add($s, new Iri(Cargo::NAMESPACE . 'colour'), $literal('x'))]), '400'],
            ['literal on object property', new Change($s, 1, [Operation::add($s, new Iri(Cargo::customsInformation), $literal('x'))]), '400'],
            ['node on datatype property', new Change($s, 1, [Operation::add($s, new Iri(Cargo::goodsDescription), new OperationObject(Cargo::Value, '_:b0'))]), '400'],
            ['invalid boolean lexical', new Change($s, 1, [Operation::add($s, new Iri(Cargo::upid), new OperationObject(Literal::XSD_BOOLEAN, 'maybe'))]), '400'],
        ];
        foreach ($cases as [$name, $change, $code]) {
            try {
                $applier->apply($from, 1, $change);
                self::fail($name . ' should be rejected');
            } catch (ChangeRejected $e) {
                self::assertSame($code, $e->errors[0]->details[0]->code, $name . ': ' . $e->getMessage());
            }
        }
        self::assertCount(7, $from->graph, 'a rejected change leaves the object untouched');
    }

    public function testAtomicity(): void
    {
        $from = self::stored(self::piece());
        $s = new Iri(self::PIECE);
        $change = new Change($s, 1, [
            Operation::add($s, new Iri(Cargo::goodsDescription), new OperationObject(Literal::XSD_STRING, 'new')),
            Operation::delete($s, new Iri(Cargo::goodsDescription), new OperationObject(Literal::XSD_STRING, 'wrong old value')),
        ]);

        try {
            (new ChangeApplier())->apply($from, 1, $change);
            self::fail();
        } catch (ChangeRejected $e) {
            self::assertCount(1, $e->errors);
            self::assertSame('ONE Record Advertisement Materials', $from->literal(Cargo::goodsDescription));
        }
    }

    public function testInferredSuperclassesAreNeitherATypeChangeNorDiffed(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        // What NE:ONE answers: the declared class plus every superclass.
        $served = ObjectBuilder::ofTypes([Cargo::Piece, Cargo::PhysicalLogisticsObject, Cargo::LogisticsObject])->set(Cargo::goodsDescription, 'Books')->build($iri);
        $wanted = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Magazines')->build($iri);

        $change = (new ChangeBuilder())->diff($served, $wanted, 1);

        self::assertNotNull($change);
        self::assertSame([Cargo::goodsDescription], $change->changedProperties(), 'no operation touches rdf:type');

        $this->expectException(ChangeException::class);
        (new ChangeBuilder())->diff($served, ObjectBuilder::of(Cargo::Shipment)->set(Cargo::goodsDescription, 'Books')->build($iri), 1);
    }

    public function testAr012ALanguageTaggedLiteralCannotBeDiffedSilently(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $tagged = new LogisticsObject($iri, new Graph([new Triple($iri, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)), new Triple($iri, new Iri(Cargo::goodsDescription), new Literal('Books', null, 'en'))]));
        $plain = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Other')->build($iri);

        try {
            (new ChangeBuilder())->diff($tagged, $plain, 1);
            self::fail('the tag cannot be expressed in an api:Change');
        } catch (ChangeException $e) {
            self::assertStringContainsString('language-tagged', $e->getMessage());
        }
    }

    public function testAr013OperationOrderDoesNotChangeTheDecision(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $piece = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Books')->build($iri);
        $link = Operation::add($iri, new Iri(Cargo::grossWeight), new OperationObject(Cargo::Value, '_:n'));
        $bogus = Operation::add(new BlankNode('n'), new Iri('https://example/unknown'), new OperationObject(Literal::XSD_STRING, 'x'));
        $applier = new ChangeApplier();

        foreach ([[$link, $bogus], [$bogus, $link]] as $operations) {
            try {
                $applier->apply($piece, 1, new Change($iri, 1, $operations));
                self::fail('an unknown property on the new Value is refused whatever the order');
            } catch (ChangeRejected $e) {
                self::assertStringContainsString('unknown', $e->errors[0]->details[0]->message ?? '');
            }
        }

        // And a valid embedded node is accepted whatever the order.
        $value = Operation::add(new BlankNode('n'), new Iri(Cargo::numericalValue), new OperationObject(Literal::XSD_DOUBLE, '20.5'));
        foreach ([[$link, $value], [$value, $link]] as $operations) {
            $result = $applier->apply($piece, 1, new Change($iri, 1, $operations));
            self::assertSame([Cargo::grossWeight], $result->changedProperties);
        }
    }

    public function testR2011ASharedEmbeddedNodeStaysOneNodeThroughAChange(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $from = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Books')->build($iri);
        $d = new Iri('internal:d');
        $v = new Iri('internal:v');
        $to = $from->withGraph(new Graph([
            ...$from->graph,
            new Triple($iri, new Iri(Cargo::dimensions), $d),
            new Triple($d, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
            new Triple($d, new Iri(Cargo::width), $v),
            new Triple($d, new Iri(Cargo::height), $v),
            new Triple($v, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple($v, new Iri(Cargo::numericalValue), Literal::integer(1)),
        ]));

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        $labels = [];
        foreach ($change->operations as $operation) {
            if ($operation->object->isBlankNode()) {
                $labels[$operation->predicate->value] = $operation->object->value;
            }
        }
        self::assertSame($labels[Cargo::width], $labels[Cargo::height], 'one blank node for the shared Value');

        $result = (new ChangeApplier())->apply($from, 1, $change);
        $dims = $result->object->graph->firstObject($iri, Cargo::dimensions);
        self::assertInstanceOf(Iri::class, $dims);
        $width = $result->object->graph->firstObject($dims, Cargo::width);
        $height = $result->object->graph->firstObject($dims, Cargo::height);
        self::assertNotNull($width);
        self::assertNotNull($height);
        self::assertTrue($width->equals($height), 'width and height still point at the same node');
        self::assertCount(1, array_filter($result->object->graph->subjects(), static fn($s): bool => $s instanceof Iri && str_starts_with($s->value, 'internal:') && $result->object->graph->firstObject($s, Cargo::numericalValue) !== null), 'exactly one Value node exists');

        // And removing the shared node deletes its triples once, the links twice.
        $back = (new ChangeBuilder())->diff($result->object, $from->withGraph(new Graph([...$from->graph])), 2);
        self::assertNotNull($back);
        $deletesOfValue = array_filter($back->operations, static fn($o): bool => $o->predicate->value === Cargo::numericalValue);
        self::assertCount(1, $deletesOfValue);
        self::assertTrue((new Comparer())->isomorphic((new ChangeApplier())->apply($result->object, 2, $back)->object->graph, $from->graph));
    }

    /**
     * piece -dimensions-> d; d -width-> v; d -height-> v; v numericalValue 1.
     */
    private function sharedValueGraph(Iri $iri, int $value = 1): Graph
    {
        $d = new Iri('internal:d');
        $v = new Iri('internal:v');

        return new Graph([
            new Triple($iri, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)),
            new Triple($iri, new Iri(Cargo::goodsDescription), Literal::string('Books')),
            new Triple($iri, new Iri(Cargo::dimensions), $d),
            new Triple($d, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
            new Triple($d, new Iri(Cargo::width), $v),
            new Triple($d, new Iri(Cargo::height), $v),
            new Triple($v, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple($v, new Iri(Cargo::numericalValue), Literal::integer($value)),
        ]);
    }

    public function testR3001EditingASharedEmbeddedNodeEmitsTheEditOnce(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $from = new LogisticsObject($iri, $this->sharedValueGraph($iri, 1));
        $to = new LogisticsObject($iri, $this->sharedValueGraph($iri, 2));

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        self::assertCount(2, $change->operations, 'one DELETE and one ADD, not two of each');
        $result = (new ChangeApplier())->apply($from, 1, $change);
        self::assertTrue((new Comparer())->isomorphic($result->object->graph, $to->graph));
        self::assertSame([Cargo::dimensions], $result->changedProperties);
    }

    public function testR3001UnlinkingOneOfTwoLinksKeepsTheNodeAndItsValue(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $from = new LogisticsObject($iri, $this->sharedValueGraph($iri));
        $toGraph = $this->sharedValueGraph($iri);
        $toGraph->remove(new Triple(new Iri('internal:d'), new Iri(Cargo::width), new Iri('internal:v')));
        $to = new LogisticsObject($iri, $toGraph);

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        self::assertCount(1, $change->operations, 'only the link goes');
        $result = (new ChangeApplier())->apply($from, 1, $change);
        self::assertTrue((new Comparer())->isomorphic($result->object->graph, $to->graph));
        $value = $result->object->graph->firstObject(new Iri('internal:v'), Cargo::numericalValue);
        self::assertInstanceOf(Literal::class, $value);
        self::assertSame('1', $value->lexical);
        $height = $result->object->graph->firstObject(new Iri('internal:d'), Cargo::height);
        self::assertInstanceOf(Iri::class, $height);
        self::assertInstanceOf(Literal::class, $result->object->graph->firstObject($height, Cargo::numericalValue), 'the value survives under height');
    }

    public function testR3001UnlinkingTheLastLinkDeletesTheNodeOnce(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $from = new LogisticsObject($iri, $this->sharedValueGraph($iri));
        $toGraph = $this->sharedValueGraph($iri);
        $toGraph->remove(new Triple(new Iri('internal:d'), new Iri(Cargo::width), new Iri('internal:v')));
        $toGraph->remove(new Triple(new Iri('internal:d'), new Iri(Cargo::height), new Iri('internal:v')));
        $toGraph->remove(new Triple(new Iri('internal:v'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)));
        $toGraph->remove(new Triple(new Iri('internal:v'), new Iri(Cargo::numericalValue), Literal::integer(1)));
        $to = new LogisticsObject($iri, $toGraph);

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        self::assertCount(3, $change->operations, 'two links and one value');
        $result = (new ChangeApplier())->apply($from, 1, $change);
        self::assertTrue((new Comparer())->isomorphic($result->object->graph, $to->graph));
        self::assertSame([], $result->object->graph->about(new Iri('internal:v')));
    }

    public function testR3001ASharedNodeEditedDifferentlyUnderEachLinkSplits(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $from = new LogisticsObject($iri, $this->sharedValueGraph($iri));
        $d = new Iri('internal:d');
        $w = new Iri('internal:w');
        $h = new Iri('internal:h');
        $to = new LogisticsObject($iri, new Graph([
            new Triple($iri, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)),
            new Triple($iri, new Iri(Cargo::goodsDescription), Literal::string('Books')),
            new Triple($iri, new Iri(Cargo::dimensions), $d),
            new Triple($d, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
            new Triple($d, new Iri(Cargo::width), $w),
            new Triple($d, new Iri(Cargo::height), $h),
            new Triple($w, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple($w, new Iri(Cargo::numericalValue), Literal::integer(2)),
            new Triple($h, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple($h, new Iri(Cargo::numericalValue), Literal::integer(3)),
        ]));

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        $result = (new ChangeApplier())->apply($from, 1, $change);
        self::assertTrue((new Comparer())->isomorphic($result->object->graph, $to->graph), $change->toJson());
    }

    public function testR3001TwoSeparateNodesBecomingOneSharedNodeStayOne(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $d = new Iri('internal:d');
        $w = new Iri('internal:w');
        $h = new Iri('internal:h');
        $from = new LogisticsObject($iri, new Graph([
            new Triple($iri, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)),
            new Triple($iri, new Iri(Cargo::goodsDescription), Literal::string('Books')),
            new Triple($iri, new Iri(Cargo::dimensions), $d),
            new Triple($d, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
            new Triple($d, new Iri(Cargo::width), $w),
            new Triple($d, new Iri(Cargo::height), $h),
            new Triple($w, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple($w, new Iri(Cargo::numericalValue), Literal::integer(2)),
            new Triple($h, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple($h, new Iri(Cargo::numericalValue), Literal::integer(3)),
        ]));
        $to = new LogisticsObject($iri, $this->sharedValueGraph($iri, 5));

        $change = (new ChangeBuilder())->diff($from, $to, 1);
        self::assertNotNull($change);
        $result = (new ChangeApplier())->apply($from, 1, $change);
        self::assertTrue((new Comparer())->isomorphic($result->object->graph, $to->graph), $change->toJson());
        $dims = $result->object->graph->firstObject($iri, Cargo::dimensions);
        self::assertInstanceOf(Iri::class, $dims);
        $width = $result->object->graph->firstObject($dims, Cargo::width);
        $height = $result->object->graph->firstObject($dims, Cargo::height);
        self::assertNotNull($width);
        self::assertNotNull($height);
        self::assertTrue($width->equals($height), 'one shared node, as in the target');
    }

    public function testR3005AChangeToANodeUnderTwoRootPropertiesReportsBoth(): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        $v = new Iri('internal:v');
        $weightFirst = new Graph([new Triple($iri, new Iri(Cargo::grossWeight), $v), ...$this->sharedValueGraph($iri)]);
        $weightLast = new Graph([...$this->sharedValueGraph($iri), new Triple($iri, new Iri(Cargo::grossWeight), $v)]);
        $change = new Change($iri, 1, [
            Operation::delete($v, new Iri(Cargo::numericalValue), OperationObject::literal(Literal::integer(1))),
            Operation::add($v, new Iri(Cargo::numericalValue), OperationObject::literal(Literal::integer(2))),
        ]);

        foreach ([$weightFirst, $weightLast] as $graph) {
            $result = (new ChangeApplier())->apply(new LogisticsObject($iri, $graph), 1, $change);
            self::assertSame([Cargo::dimensions, Cargo::grossWeight], $result->changedProperties, 'both, whatever the insertion order');
        }

        // Two paths under the same root property (width and height both reach v) report it once. An embedded
        // cycle cannot be built within the ontology's ranges any more, so termination is exercised by the shared
        // node's two paths rather than by an invalid loop.
        $result = (new ChangeApplier())->apply(new LogisticsObject($iri, $this->sharedValueGraph($iri)), 1, $change);
        self::assertSame([Cargo::dimensions], $result->changedProperties);
    }

    private const array DIMENSION_PROPERTIES = ['height' => Cargo::height, 'width' => Cargo::width, 'length' => Cargo::length];

    /**
     * Graphs described by slot: property => [node label, value]; the same label in two slots is one shared node.
     *
     * @param array<string, array{string, int}> $slots
     */
    private static function dimensionsGraph(Iri $iri, array $slots, string $prefix = 'internal:'): Graph
    {
        $d = new Iri($prefix . 'd');
        $graph = new Graph([
            new Triple($iri, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)),
            new Triple($iri, new Iri(Cargo::goodsDescription), Literal::string('Books')),
            new Triple($iri, new Iri(Cargo::dimensions), $d),
            new Triple($d, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
        ]);
        $nodes = [];
        foreach ($slots as $property => [$label, $value]) {
            $node = new Iri($prefix . $label);
            $graph->add(new Triple($d, new Iri(self::DIMENSION_PROPERTIES[$property]), $node));
            if (!isset($nodes[$label])) {
                $nodes[$label] = true;
                $graph->add(new Triple($node, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)));
                $graph->add(new Triple($node, new Iri(Cargo::numericalValue), Literal::integer($value)));
            }
        }

        return $graph;
    }

    /**
     * @return iterable<string, array{array<string, array{string, int}>, array<string, array{string, int}>}>
     */
    public static function sharedNodeTransitions(): iterable
    {
        yield 'shared 1 -> height 1 / width 2, separate' => [['height' => ['v', 1], 'width' => ['v', 1]], ['height' => ['h', 1], 'width' => ['w', 2]]];
        yield 'shared 1 -> height 2 / width 1, separate' => [['height' => ['v', 1], 'width' => ['v', 1]], ['height' => ['h', 2], 'width' => ['w', 1]]];
        yield 'separate 1/2 -> shared 1' => [['height' => ['h', 1], 'width' => ['w', 2]], ['height' => ['v', 1], 'width' => ['v', 1]]];
        yield 'separate 1/2 -> shared 2' => [['height' => ['h', 1], 'width' => ['w', 2]], ['height' => ['v', 2], 'width' => ['v', 2]]];
        yield 'height only -> both share that value' => [['height' => ['h', 1]], ['height' => ['v', 1], 'width' => ['v', 1]]];
        yield 'shared 1 -> two separate 1s' => [['height' => ['v', 1], 'width' => ['v', 1]], ['height' => ['h', 1], 'width' => ['w', 1]]];
        yield 'two separate 1s -> shared 1' => [['height' => ['h', 1], 'width' => ['w', 1]], ['height' => ['v', 1], 'width' => ['v', 1]]];
        yield 'width only -> both share value 2' => [['width' => ['w', 1]], ['height' => ['v', 2], 'width' => ['v', 2]]];
        yield 'shared 1 -> shared 2' => [['height' => ['v', 1], 'width' => ['v', 1]], ['height' => ['v', 2], 'width' => ['v', 2]]];
        yield 'shared -> height only' => [['height' => ['v', 1], 'width' => ['v', 1]], ['height' => ['v', 1]]];
        yield 'three sharing -> length alone changes' => [['height' => ['v', 1], 'width' => ['v', 1], 'length' => ['v', 1]], ['height' => ['v', 1], 'width' => ['v', 1], 'length' => ['l', 2]]];
        yield 'three separate -> all shared' => [['height' => ['h', 1], 'width' => ['w', 2], 'length' => ['l', 3]], ['height' => ['v', 3], 'width' => ['v', 3], 'length' => ['v', 3]]];
    }

    /**
     * R4-001: the result must read the target's value through every property and share nodes
     * exactly as the target does, whatever the embedded ids are called and in either direction.
     *
     * @param array<string, array{string, int}> $before
     * @param array<string, array{string, int}> $after
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('sharedNodeTransitions')]
    public function testR4001SharedNodeTransitionsPreserveValuesAndSharing(array $before, array $after): void
    {
        $iri = new Iri('https://1r.example.com/logistics-objects/p1');
        foreach ([[$before, $after], [$after, $before]] as [$fromSlots, $toSlots]) {
            foreach (['internal:', 'internal:other-'] as $targetPrefix) {
                $from = new LogisticsObject($iri, self::dimensionsGraph($iri, $fromSlots));
                $to = new LogisticsObject($iri, self::dimensionsGraph($iri, $toSlots, $targetPrefix));
                $change = (new ChangeBuilder())->diff($from, $to, 1);
                self::assertNotNull($change, 'a topology change is a change');
                $result = (new ChangeApplier())->apply($from, 1, $change)->object->graph;
                $d = $result->firstObject($iri, Cargo::dimensions);
                self::assertInstanceOf(Iri::class, $d);
                $nodes = [];
                foreach (self::DIMENSION_PROPERTIES as $property => $iriOfProperty) {
                    $node = $result->firstObject($d, $iriOfProperty);
                    if (!isset($toSlots[$property])) {
                        self::assertNull($node, $property . ' is gone');
                        continue;
                    }
                    self::assertInstanceOf(Iri::class, $node, $property . ' present');
                    $value = $result->firstObject($node, Cargo::numericalValue);
                    self::assertInstanceOf(Literal::class, $value);
                    self::assertSame((string) $toSlots[$property][1], $value->lexical, $property . ' reads the target value: ' . $change->toJson());
                    $nodes[$property] = $node->value;
                }
                foreach ($nodes as $a => $nodeA) {
                    foreach ($nodes as $b => $nodeB) {
                        self::assertSame($toSlots[$a][0] === $toSlots[$b][0], $nodeA === $nodeB, \sprintf('%s and %s share a node exactly when the target does', $a, $b));
                    }
                }
                self::assertTrue((new Comparer())->isomorphic($result, $to->graph), 'no stray nodes remain');
            }
        }
    }

    public function testR7007IllTypedLiteralsAreRefusedByGrammarAndByRange(): void
    {
        $uld = ObjectBuilder::of(Cargo::ULD)->set(Cargo::goodsDescription, 'Container')->build(new Iri('https://1r.example.com/logistics-objects/u1'));
        $applier = new ChangeApplier();
        $add = static fn(string $property, string $datatype, string $value): Change => new Change($uld->iri, 1, [Operation::add($uld->iri, new Iri($property), new OperationObject($datatype, $value))]);

        foreach ([
            'a fraction is not an integer' => $add(Cargo::numberOfDoors, Literal::XSD_INTEGER, '1.5'),
            'an exponent is not a decimal' => $add(Cargo::numberOfDoors, Literal::XSD_DECIMAL, '1e3'),
            'a string where the range wants an integer' => $add(Cargo::numberOfDoors, Literal::XSD_STRING, '2'),
            'a double where the range wants an integer' => $add(Cargo::numberOfDoors, Literal::XSD_DOUBLE, '2.0'),
        ] as $label => $change) {
            try {
                $applier->apply($uld, 1, $change);
                self::fail($label);
            } catch (ChangeRejected $e) {
                self::assertSame('Invalid resource', $e->errors[0]->title, $label);
            }
        }
        $result = $applier->apply($uld, 1, $add(Cargo::numberOfDoors, Literal::XSD_INTEGER, '2'));
        $doors = $result->object->graph->firstObject($uld->iri, Cargo::numberOfDoors);
        self::assertInstanceOf(Literal::class, $doors);
        self::assertSame(['2', Literal::XSD_INTEGER], [$doors->lexical, $doors->datatype]);

        // Grammars, independently of a property.
        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('-12', Literal::XSD_INTEGER)));
        self::assertFalse(ChangeApplier::lexicallyValid(new Literal('12.0', Literal::XSD_INTEGER)));
        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('12.50', Literal::XSD_DECIMAL)));
        self::assertFalse(ChangeApplier::lexicallyValid(new Literal('1E2', Literal::XSD_DECIMAL)));
        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('1E2', Literal::XSD_DOUBLE)));
        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('-INF', Literal::XSD_DOUBLE)));
        self::assertFalse(ChangeApplier::lexicallyValid(new Literal('2026-02-30T00:00:00Z', Literal::XSD_DATETIME)));
        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('2026-10-02T24:00:00Z', Literal::XSD_DATETIME)));
    }

    public function testR8002DerivedIntegerTypesSatisfyAnIntegerRangeWithinTheirBounds(): void
    {
        $xsd = 'http://www.w3.org/2001/XMLSchema#';
        self::assertTrue(\LambdaTwelve\OneRecord\Rdf\Xsd::satisfies($xsd . 'int', [Literal::XSD_INTEGER]), 'an int is an integer');
        self::assertTrue(\LambdaTwelve\OneRecord\Rdf\Xsd::satisfies($xsd . 'unsignedByte', [Literal::XSD_DECIMAL]));
        self::assertTrue(\LambdaTwelve\OneRecord\Rdf\Xsd::satisfies($xsd . 'short', [Literal::XSD_DOUBLE]), 'a whole number is a number');
        self::assertTrue(\LambdaTwelve\OneRecord\Rdf\Xsd::satisfies(Literal::XSD_DECIMAL, [Literal::XSD_DOUBLE]));
        self::assertFalse(\LambdaTwelve\OneRecord\Rdf\Xsd::satisfies(Literal::XSD_INTEGER, [$xsd . 'int']), 'but not the other way round');
        self::assertFalse(\LambdaTwelve\OneRecord\Rdf\Xsd::satisfies(Literal::XSD_DOUBLE, [Literal::XSD_INTEGER]));
        self::assertFalse(\LambdaTwelve\OneRecord\Rdf\Xsd::satisfies(Literal::XSD_STRING, [Literal::XSD_INTEGER]));

        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('2147483647', $xsd . 'int')));
        self::assertFalse(ChangeApplier::lexicallyValid(new Literal('2147483648', $xsd . 'int')), 'out of int\'s bounds');
        self::assertFalse(ChangeApplier::lexicallyValid(new Literal('200', $xsd . 'byte')));
        self::assertFalse(ChangeApplier::lexicallyValid(new Literal('-1', $xsd . 'nonNegativeInteger')));
        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('0', $xsd . 'nonNegativeInteger')));
        self::assertFalse(ChangeApplier::lexicallyValid(new Literal('0', $xsd . 'positiveInteger')));
        self::assertTrue(ChangeApplier::lexicallyValid(new Literal('99999999999999999999', Literal::XSD_INTEGER)), 'xsd:integer is unbounded');

        // A change adding "2"^^xsd:int to an integer-ranged property is accepted (R8-002).
        $uld = ObjectBuilder::of(Cargo::ULD)->set(Cargo::goodsDescription, 'Container')->build(new Iri('https://1r.example.com/logistics-objects/u1'));
        $result = (new ChangeApplier())->apply($uld, 1, new Change($uld->iri, 1, [Operation::add($uld->iri, new Iri(Cargo::numberOfDoors), new OperationObject($xsd . 'int', '2'))]));
        $doors = $result->object->graph->firstObject($uld->iri, Cargo::numberOfDoors);
        self::assertInstanceOf(Literal::class, $doors);
        self::assertSame($xsd . 'int', $doors->datatype, 'the value keeps the datatype it came with');
    }

    public function testR10ChangesCannotIntroduceUntypedOrMistypedEmbeddedNodes(): void
    {
        $piece = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Books')->build(new Iri('https://1r.example.com/logistics-objects/p1'));
        $applier = new ChangeApplier();
        $cases = [
            'a class the ontology does not know' => [Operation::add($piece->iri, new Iri(Cargo::dimensions), new OperationObject('https://example.com/FakeClass', '_:b1')), Operation::add(new BlankNode('b1'), new Iri('https://example.com/fakeProp'), OperationObject::literal(Literal::string('injected')))],
            'a logistics object as an embedded node' => [Operation::add($piece->iri, new Iri(Cargo::dimensions), new OperationObject(Cargo::Piece, '_:b1'))],
            'a Person where Dimensions is expected' => [Operation::add($piece->iri, new Iri(Cargo::dimensions), new OperationObject(Cargo::Person, '_:b1')), Operation::add(new BlankNode('b1'), new Iri(Cargo::firstName), OperationObject::literal(Literal::string('Alice')))],
        ];
        foreach ($cases as $label => $operations) {
            try {
                $applier->apply($piece, 1, new Change($piece->iri, 1, $operations));
                self::fail($label);
            } catch (ChangeRejected $e) {
                self::assertSame('Invalid resource', $e->errors[0]->title, $label);
            }
        }
        // The spec's own C2 shape still works: a Value under grossWeight.
        $result = $applier->apply($piece, 1, new Change($piece->iri, 1, [Operation::add($piece->iri, new Iri(Cargo::grossWeight), new OperationObject(Cargo::Value, '_:b1')), Operation::add(new BlankNode('b1'), new Iri(Cargo::numericalValue), OperationObject::literal(Literal::double(20.0)))]));
        self::assertInstanceOf(Iri::class, $result->object->graph->firstObject($piece->iri, Cargo::grossWeight));
    }

    public function testR10003IntegerTypesCompareAsOneNumberInDeletesAndAdds(): void
    {
        $xsd = 'http://www.w3.org/2001/XMLSchema#';
        $uld = ObjectBuilder::of(Cargo::ULD)->set(Cargo::numberOfDoors, Literal::integer(2))->build(new Iri('https://1r.example.com/logistics-objects/u1'));
        $applier = new ChangeApplier();
        foreach (['byte', 'unsignedInt', 'int', 'negativeInteger'] as $type) {
            $literal = new Literal($type === 'negativeInteger' ? '-2' : '2', $xsd . $type);
            if ($type !== 'negativeInteger') {
                $result = $applier->apply($uld, 1, new Change($uld->iri, 1, [Operation::delete($uld->iri, new Iri(Cargo::numberOfDoors), OperationObject::literal($literal))]));
                self::assertNull($result->object->graph->firstObject($uld->iri, Cargo::numberOfDoors), 'a delete spelled as ' . $type . ' matches the stored xsd:integer');
                try {
                    $applier->apply($uld, 1, new Change($uld->iri, 1, [Operation::add($uld->iri, new Iri(Cargo::numberOfDoors), OperationObject::literal($literal))]));
                    self::fail('an add spelled as ' . $type . ' duplicates the stored value');
                } catch (ChangeRejected $e) {
                    self::assertStringContainsString('already present', $e->errors[0]->details[0]->message ?? '');
                }
            }
        }
    }
}
