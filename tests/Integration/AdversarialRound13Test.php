<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Review 13: a code list counts as a class when a property's range is checked.
 */
#[CoversNothing]
final class AdversarialRound13Test extends ServerTestCase
{
    public function testR13001AUnitFromTheWrongCodeListIsRefusedOnCreation(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $post = fn(string $unit, ?string $type): \Psr\Http\Message\ResponseInterface => $this->request('POST', '/logistics-objects', self::HOLDER, [], json_encode([
            '@context' => ['cargo' => Cargo::NAMESPACE, 'xsd' => 'http://www.w3.org/2001/XMLSchema#'],
            '@type' => 'cargo:Piece',
            'cargo:grossWeight' => ['@type' => 'cargo:Value', 'cargo:numericalValue' => ['@type' => 'xsd:double', '@value' => '1.5'], 'cargo:unit' => ['@id' => $unit] + ($type === null ? [] : ['@type' => $type])],
        ], JSON_THROW_ON_ERROR));
        $currencies = 'https://onerecord.iata.org/ns/code-lists/CurrencyCode';
        $units = 'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode';

        self::assertError($post($currencies . '#EUR', $currencies), 400, 'Invalid resource');
        self::assertError($post($currencies . '#EUR', null), 400, 'Invalid resource');
        self::assertError($post($units . '#KGM', $currencies), 400, 'Invalid resource');
        self::assertSame(201, $post($units . '#KGM', $units)->getStatusCode());
        self::assertSame(201, $post($units . '#KGM', null)->getStatusCode());
    }
}
