<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Rdf;

use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Xsd;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Xsd::class)]
final class XsdTest extends TestCase
{
    private const string XSD = 'http://www.w3.org/2001/XMLSchema#';

    public function testEveryIntegerTypeIsKnownAndNormalisedAlike(): void
    {
        $types = ['integer', 'long', 'int', 'short', 'byte', 'nonNegativeInteger', 'positiveInteger', 'unsignedLong', 'unsignedInt', 'unsignedShort', 'unsignedByte', 'nonPositiveInteger', 'negativeInteger'];
        $comparer = new Comparer();
        foreach ($types as $type) {
            self::assertTrue(Xsd::isIntegerType(self::XSD . $type), $type);
            $lexical = \in_array($type, ['nonPositiveInteger', 'negativeInteger'], true) ? '-2' : '2';
            self::assertEquals(new Literal($lexical, Literal::XSD_INTEGER), $comparer->normaliseLiteral(new Literal($lexical, self::XSD . $type)), 'the comparer takes its integer family from Xsd (R10-003): ' . $type);
        }
        self::assertFalse(Xsd::isIntegerType(Literal::XSD_DECIMAL));
        self::assertFalse(Xsd::isIntegerType(Literal::XSD_STRING));
    }

    public function testDateOffsetsAreBoundedLikeDateTimeOffsets(): void
    {
        self::assertTrue(Xsd::lexicallyValid(new Literal('2026-10-05', self::XSD . 'date')));
        self::assertTrue(Xsd::lexicallyValid(new Literal('2026-10-05Z', self::XSD . 'date')));
        self::assertTrue(Xsd::lexicallyValid(new Literal('2026-10-05+14:00', self::XSD . 'date')));
        self::assertFalse(Xsd::lexicallyValid(new Literal('2026-10-05+14:30', self::XSD . 'date')), 'D10-002');
        self::assertFalse(Xsd::lexicallyValid(new Literal('2026-10-05+25:00', self::XSD . 'date')));
        self::assertFalse(Xsd::lexicallyValid(new Literal('2026-10-05+99:99', self::XSD . 'date')));
        self::assertFalse(Xsd::lexicallyValid(new Literal('2026-02-30', self::XSD . 'date')));
    }
}
