<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Change;

use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\ChangeException;
use LambdaTwelve\OneRecord\Change\Operation;
use LambdaTwelve\OneRecord\Change\OperationKind;
use LambdaTwelve\OneRecord\Change\OperationObject;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Change::class)]
#[CoversClass(Operation::class)]
#[CoversClass(OperationObject::class)]
#[CoversClass(OperationKind::class)]
#[CoversClass(ChangeException::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Api\Error::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Api\ErrorDetail::class)]
#[UsesClass(JsonLd::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Expander::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\ExpandedDocument::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Context::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Json::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\JsonLdException::class)]
#[UsesClass(Comparer::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Diff::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(BlankNode::class)]
#[UsesClass(Literal::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Triple::class)]
final class ChangeTest extends TestCase
{
    private const string PIECE = 'https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b7c';

    /**
     * @return iterable<string, array{string}>
     */
    public static function specChanges(): iterable
    {
        foreach (['Change_example1', 'Change_example2', 'Change_example3', 'Change_example4', 'Change_example5'] as $name) {
            yield $name => [__DIR__ . '/../../Fixtures/spec/2026-07/' . $name . '.json'];
        }
    }

    #[DataProvider('specChanges')]
    public function testReadsTheSpecExamplesAndWritesThemBackEquivalently(string $file): void
    {
        $json = (string) file_get_contents($file);
        $change = Change::fromJsonLd($json);

        self::assertSame(self::PIECE, $change->logisticsObject->value);
        self::assertGreaterThan(0, $change->revision);

        $written = Change::fromJsonLd($change->toJson());
        self::assertEquals($change, $written, 'model survives a write and read');
        $diff = (new Comparer())->compare(JsonLd::expand($json)->graph, JsonLd::expand($change->toJsonLd())->graph);
        self::assertTrue($diff->isEqual(), $diff->describe());
    }

    public function testExampleOneIsThreeOperationsOnThePiece(): void
    {
        $change = Change::fromJsonLd((string) file_get_contents(__DIR__ . '/../../Fixtures/spec/2026-07/Change_example1.json'));

        self::assertSame(1, $change->revision);
        self::assertSame('Update goods description and coload', $change->description);
        self::assertFalse($change->notifyRequestStatusChange);
        self::assertCount(3, $change->operations);
        self::assertSame([Cargo::coload, Cargo::goodsDescription], $change->changedProperties());

        $kinds = array_map(static fn(Operation $o): string => $o->kind->name . ' ' . $o->predicate->localName() . ' ' . $o->object->value, $change->operations);
        sort($kinds);
        self::assertSame(['Add coload true', 'Add goodsDescription ONE Record Advertisement Materials', 'Delete coload false'], $kinds);
        self::assertTrue($change->operations[0]->object->isLiteral());
        self::assertFalse($change->operations[0]->object->isBlankNode());
    }

    public function testExampleTwoIntroducesABlankNode(): void
    {
        $change = Change::fromJsonLd((string) file_get_contents(__DIR__ . '/../../Fixtures/spec/2026-07/Change_example2.json'));

        $link = array_values(array_filter($change->operations, static fn(Operation $o): bool => $o->predicate->value === Cargo::grossWeight))[0];
        self::assertSame(Cargo::Value, $link->object->datatype);
        self::assertTrue($link->object->isBlankNode());
        self::assertSame('_:b0', $link->object->value);
        $onBlank = array_values(array_filter($change->operations, static fn(Operation $o): bool => $o->subject instanceof BlankNode));
        self::assertCount(2, $onBlank);
        self::assertSame('_:b0', $onBlank[0]->subjectString());
    }

    public function testWritesTheShapeIataUses(): void
    {
        $change = new Change(new Iri(self::PIECE), 3, [
            Operation::delete(new Iri(self::PIECE), new Iri(Cargo::coload), OperationObject::literal(Literal::boolean(false))),
            Operation::add(new Iri(self::PIECE), new Iri(Cargo::coload), OperationObject::literal(Literal::boolean(true))),
        ], 'Flip coload', notifyRequestStatusChange: true, verificationRequests: [new Iri('https://1r.example.com/action-requests/v1')]);

        $json = $change->toJsonLd();
        self::assertSame('api:Change', $json['@type']);
        self::assertSame(['@id' => self::PIECE], $json['api:hasLogisticsObject']);
        self::assertSame(['@type' => 'xsd:positiveInteger', '@value' => '3'], $json['api:hasRevision']);
        $operations = $json['api:hasOperation'];
        self::assertIsArray($operations);
        $first = $operations[0];
        self::assertIsArray($first);
        self::assertSame(['@id' => 'api:DELETE'], $first['api:op']);
        self::assertSame([['@type' => 'api:OperationObject', 'api:hasDatatype' => Literal::XSD_BOOLEAN, 'api:hasValue' => 'false']], $first['api:o']);
        self::assertTrue($json['api:notifyRequestStatusChange']);
        self::assertSame([['@id' => 'https://1r.example.com/action-requests/v1']], $json['api:hasVerificationRequest']);
        self::assertEquals($change, Change::fromJsonLd($json));
        // A language tag cannot travel in an operation object; pretending it is an xsd:string would change the value (AR-012).
        $this->expectException(ChangeException::class);
        OperationObject::literal(new Literal('x', null, 'en'));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string}>
     */
    public static function malformed(): iterable
    {
        $ctx = ['api' => 'https://onerecord.iata.org/ns/api#', 'xsd' => 'http://www.w3.org/2001/XMLSchema#'];
        $op = ['@type' => 'api:Operation', 'api:op' => ['@id' => 'api:ADD'], 'api:s' => self::PIECE, 'api:p' => Cargo::coload, 'api:o' => [['@type' => 'api:OperationObject', 'api:hasDatatype' => Literal::XSD_BOOLEAN, 'api:hasValue' => 'true']]];
        $base = ['@context' => $ctx, '@type' => 'api:Change', 'api:hasLogisticsObject' => ['@id' => self::PIECE], 'api:hasOperation' => [$op], 'api:hasRevision' => 1];
        yield 'not a change' => [[...$base, '@type' => 'api:Subscription'], 'not an api:Change'];
        yield 'no target' => [array_diff_key($base, ['api:hasLogisticsObject' => 1]), 'api:hasLogisticsObject'];
        yield 'target as literal' => [[...$base, 'api:hasLogisticsObject' => self::PIECE], 'api:hasLogisticsObject'];
        yield 'no revision' => [array_diff_key($base, ['api:hasRevision' => 1]), 'api:hasRevision'];
        yield 'zero revision' => [[...$base, 'api:hasRevision' => 0], 'positive integer'];
        yield 'no operations' => [array_diff_key($base, ['api:hasOperation' => 1]), 'at least one operation'];
        yield 'bad op' => [[...$base, 'api:hasOperation' => [[...$op, 'api:op' => ['@id' => 'api:REPLACE']]]], 'api:ADD or api:DELETE'];
        yield 'bad subject' => [[...$base, 'api:hasOperation' => [[...$op, 'api:s' => 'not an iri']]], 'api:s'];
        yield 'bad predicate' => [[...$base, 'api:hasOperation' => [[...$op, 'api:p' => 'coload']]], 'api:p'];
        yield 'no object' => [[...$base, 'api:hasOperation' => [array_diff_key($op, ['api:o' => 1])]], 'api:o'];
        yield 'object without value' => [[...$base, 'api:hasOperation' => [[...$op, 'api:o' => [['@type' => 'api:OperationObject', 'api:hasDatatype' => Literal::XSD_BOOLEAN]]]]], 'api:hasValue'];
        yield 'object without datatype' => [[...$base, 'api:hasOperation' => [[...$op, 'api:o' => [['@type' => 'api:OperationObject', 'api:hasValue' => 'x']]]]], 'api:hasDatatype'];
        yield 'invalid json-ld' => [[...$base, '@graph' => []], 'Invalid body request'];
    }

    /**
     * @param array<string, mixed> $document
     */
    #[DataProvider('malformed')]
    public function testRejectsMalformedChanges(array $document, string $message): void
    {
        try {
            Change::fromJsonLd($document);
            self::fail('expected a ChangeException');
        } catch (ChangeException $e) {
            self::assertStringContainsString($message, $e->getMessage() . ' ' . ($e->errors[0]->details[0]->property ?? ''));
            self::assertNotEmpty($e->errors);
            self::assertSame('400', $e->errors[0]->details[0]->code);
        }
    }
}
