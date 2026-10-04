<?php

declare(strict_types=1);

// Loads the package from a consumer's autoloader (no development dependencies installed) and
// exercises the runtime once: vocabulary, model, JSON-LD, change and server wiring. Fails loudly.
// Usage: php tools/consumer-smoke.php /path/to/consumer/vendor/autoload.php

$autoload = $argv[1] ?? null;
if ($autoload === null || !is_file($autoload)) {
    fwrite(STDERR, "Usage: consumer-smoke.php <vendor/autoload.php>\n");
    exit(2);
}
require $autoload;

use LambdaTwelve\OneRecord\Change\ChangeApplier;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;

$fail = static function (string $what): never {
    fwrite(STDERR, "consumer smoke failed: {$what}\n");
    exit(1);
};

class_exists('PHPUnit\Framework\TestCase') && $fail('PHPUnit is installed; this is not a --no-dev install');
Vocabulary::default()->isClass(Cargo::Piece) || $fail('the generated vocabulary did not load');

$iri = new Iri('https://consumer.example/logistics-objects/p1');
$piece = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Books')->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM))->build($iri);
$copy = LogisticsObject::fromJsonLd($piece->toJson(), $iri);
$piece->isSameAs($copy) || $fail('the model did not round-trip through JSON-LD');
(new Comparer())->isomorphic(JsonLd::expand($piece->toJson())->graph, $copy->graph) || $fail('the comparer disagrees with the model');

$changed = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Magazines')->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM))->build($iri);
$change = (new ChangeBuilder())->diff($piece, $changed, 1) ?? $fail('no change computed');
$result = (new ChangeApplier())->apply($piece, 1, $change);
$result->object->isSameAs($changed) || $fail('the applied change did not reproduce the target');

ServerConfig::problems(['baseUrl' => 'https://consumer.example', 'dataHolder' => 'https://consumer.example/logistics-objects/me']) === [] || $fail('a valid configuration reported problems');

echo "consumer smoke ok\n";
