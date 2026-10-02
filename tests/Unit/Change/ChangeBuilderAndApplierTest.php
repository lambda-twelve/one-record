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
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
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
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(BlankNode::class)]
#[UsesClass(Literal::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Triple::class)]
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
        $graph = new \LambdaTwelve\OneRecord\Rdf\Graph();
        $minter = new Uuid5EmbeddedIdMinter();
        $ids = [];
        foreach ($object->graph as $triple) {
            $s = $triple->subject instanceof BlankNode ? ($ids[$triple->subject->label] ??= $minter->mint($object->iri, 'seed:' . $triple->subject->label)) : $triple->subject;
            $o = $triple->object instanceof BlankNode ? ($ids[$triple->object->label] ??= $minter->mint($object->iri, 'seed:' . $triple->object->label)) : $triple->object;
            $graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($s, $triple->predicate, $o));
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
            ['type of the object', new Change($s, 1, [Operation::add($s, new Iri(\LambdaTwelve\OneRecord\Rdf\Graph::RDF_TYPE), new OperationObject(Cargo::Shipment, Cargo::Shipment))]), '400'],
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
}
