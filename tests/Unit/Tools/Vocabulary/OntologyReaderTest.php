<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Tools\Vocabulary;

use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\OntologyModel;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\OntologyReader;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\PropertyDef;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\PropertyKind;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Turtle\TurtleParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OntologyReader::class)]
#[UsesClass(TurtleParser::class)]
#[UsesClass(OntologyModel::class)]
#[UsesClass(PropertyDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\ClassDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\IndividualDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\CodeListDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Graph::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Iri::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\BlankNode::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Literal::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Triple::class)]
final class OntologyReaderTest extends TestCase
{
    private const string FIXTURES = __DIR__ . '/../../../Fixtures/ontologies';

    private function read(string $file): OntologyModel
    {
        return (new OntologyReader())->read((new TurtleParser())->parse((string) file_get_contents(self::FIXTURES . '/' . $file)));
    }

    public function testReadsTheCargoOntologyShape(): void
    {
        $model = $this->read('2025-07/IATA-1R-DM-Ontology.ttl');
        $cargo = 'https://onerecord.iata.org/ns/cargo#';

        self::assertSame('https://onerecord.iata.org/ns/cargo', $model->ontologyIri);
        self::assertSame('3.2', $model->versionInfo);
        self::assertSame('https://onerecord.iata.org/ns/cargo/3.2', $model->versionIri);
        self::assertSame(['EventTimeType', 'LogisticsObject', 'OldThing', 'PhysicalLogisticsObject', 'Piece', 'Value'], array_values(array_map(static fn($c): string => $c->name, $model->classes)));
        self::assertCount(6, $model->classes, 'code-list classes declared in the cargo file are not cargo classes');

        $piece = $model->classes[$cargo . 'Piece'];
        self::assertSame([$cargo . 'PhysicalLogisticsObject'], $piece->parents);
        self::assertSame([$cargo . 'coload' => 'http://www.w3.org/2001/XMLSchema#boolean', $cargo . 'grossWeight' => $cargo . 'Value'], $piece->properties);
        self::assertSame('Piece, a "physical" unit of cargo', $piece->comment);
        self::assertFalse($piece->deprecated);
        self::assertTrue($model->classes[$cargo . 'OldThing']->deprecated);
        self::assertSame("Unit\nand value", $model->classes[$cargo . 'Value']->comment);

        $grossWeight = $model->properties[$cargo . 'grossWeight'];
        self::assertSame(PropertyKind::Object, $grossWeight->kind);
        self::assertSame([$cargo . 'Value'], $grossWeight->ranges);
        self::assertSame([$cargo . 'Piece'], $grossWeight->domains);
        self::assertSame('Weight details', $grossWeight->comment, 'the "Domain" note is not part of the comment');

        self::assertSame([PropertyDef::ANY_DOMAIN], $model->properties[$cargo . 'goodsDescription']->domains);
        self::assertSame(['http://www.w3.org/2001/XMLSchema#string'], $model->properties[$cargo . 'airlineCode']->ranges, 'derived datatypes reduce to their base');
        self::assertSame(PropertyKind::Datatype, $model->properties[$cargo . 'coload']->kind);

        $actual = $model->individuals[$cargo . 'ACTUAL'];
        self::assertSame([$cargo . 'EventTimeType'], $actual->types);
        self::assertSame('Used when a time is actual', $actual->comment);
    }

    public function testReadsCodeLists(): void
    {
        $model = $this->read('2025-07/IATA-1R-CL-Ontology.ttl');
        $codes = 'https://onerecord.iata.org/ns/code-lists/';

        self::assertSame('1.1.0', $model->versionInfo);
        self::assertCount(3, $model->codeLists);
        self::assertSame([], $model->classes);

        $units = $model->codeLists[$codes . 'MeasurementUnitCode'];
        self::assertTrue($units->open, 'no owl:oneOf means an open list');
        self::assertSame(['CMT' => 'Centimetre', 'KGM' => 'Kilogram', 'LBR' => 'Pound'], $units->codes);

        $weights = $model->codeLists[$codes . 'WeightUnitCode'];
        self::assertFalse($weights->open);
        self::assertSame(['KGM', 'LBR'], array_keys($weights->codes), 'members typed into several lists appear in each');

        self::assertSame(['0', '10'], array_map('strval', array_keys($model->codeLists[$codes . 'DensityGroupCode']->codes)));
    }

    public function testReadsTheApiOntologyWithDomainsAndUnions(): void
    {
        $model = $this->read('2025-07/ONE-Record-API-Ontology.ttl');
        $api = 'https://onerecord.iata.org/ns/api#';

        self::assertSame('2.2.0', $model->versionInfo);
        self::assertSame([$api . 'Change'], $model->properties[$api . 'hasOperation']->domains);
        self::assertSame([$api . 'Change', $api . 'Subscription'], $model->properties[$api . 'hasDescription']->domains, 'owl:unionOf domains are flattened');
        self::assertSame([], $model->properties[$api . 'hasLogisticsObject']->domains);
        self::assertSame(['https://onerecord.iata.org/ns/cargo#LogisticsObject'], $model->properties[$api . 'hasLogisticsObject']->ranges);
        self::assertSame([$api . 'hasOperation' => $api . 'Operation'], $model->classes[$api . 'Change']->properties);
        self::assertSame([$api . 'RequestStatus'], $model->individuals[$api . 'REQUEST_PENDING']->types);
    }
}
