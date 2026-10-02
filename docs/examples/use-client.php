<?php

declare(strict_types=1);

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Client\OneRecordClient;
use LambdaTwelve\OneRecord\Client\OneRecordHttpException;
use LambdaTwelve\OneRecord\Client\StaticTokenProvider;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\InMemory\InMemoryServer;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use LambdaTwelve\OneRecord\Server\SystemClock;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// --- A partner's server to talk to. In real use this is someone else's
// ONE Record server on the network; here it is this package's own server,
// in-process, so the example runs anywhere.
$factory = new Psr17Factory();
$partnerHolder = new Iri('https://1r.partner.example/logistics-objects/airline');
$partner = new InMemoryServer(
    new ServerConfig('https://1r.partner.example', $partnerHolder),
    new class implements Authenticator {
        public function authenticate(ServerRequestInterface $request): ?Agent
        {
            // Trusts the bearer token as the agent IRI. Production servers verify RS256 tokens.
            return preg_match('/^Bearer (.+)$/', $request->getHeaderLine('Authorization'), $m) === 1 ? new Agent(new Iri($m[1])) : null;
        }
    },
    new SystemClock(),
    new class implements EventDispatcherInterface {
        public function dispatch(object $event): object
        {
            return $event;
        }
    },
    $factory,
    $factory,
);
$partner->policy->addInternal($partnerHolder);
$piece = (new DataHolder($partner->services))->create(
    ObjectBuilder::of(Cargo::Piece)
        ->set(Cargo::goodsDescription, 'Machine parts')
        ->set(Cargo::grossWeight, Values::quantity(190.5, MeasurementUnitCode::KGM))
        ->build(new Iri('https://1r.partner.example/logistics-objects/piece-1')),
);
$us = new Iri('https://1r.example.com/logistics-objects/forwarder');
$partner->policy->allow($us, $piece->object->iri, [Permission::GetLogisticsObject, Permission::PatchLogisticsObject]);

// --- Any PSR-18 client works (Guzzle, Symfony HttpClient, ...). This one
// hands requests to the in-process server.
$http = new class ($partner->handler) implements ClientInterface {
    public function __construct(private readonly Psr\Http\Server\RequestHandlerInterface $handler) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->handler->handle(new ServerRequest($request->getMethod(), $request->getUri(), $request->getHeaders(), (string) $request->getBody()));
    }
};

// --- The client: PSR-18 + PSR-17 + a token provider + the partner's endpoint.
// ClientCredentialsTokenProvider fetches OAuth 2.0 tokens; a fixed token is used here.
$client = new OneRecordClient($http, $factory, $factory, new StaticTokenProvider($us->value), 'https://1r.partner.example');

$information = $client->serverInformation();
echo 'Partner: ', $information->dataHolder->value, ' speaking API ', $client->apiVersion()->value, "\n";

$read = $client->getLogisticsObject($piece->object->iri);
$current = $read->object ?? throw new RuntimeException('GET always carries the object.');
echo 'Piece revision ', $read->revision, ': ', $current->literal(Cargo::goodsDescription), "\n";

// Ask for a change: diff what we read against what we want, send the Change.
$wanted = ObjectBuilder::of(Cargo::Piece)
    ->set(Cargo::goodsDescription, 'Machine parts, repacked')
    ->set(Cargo::grossWeight, Values::quantity(192.0, MeasurementUnitCode::KGM))
    ->build($piece->object->iri);
$change = (new ChangeBuilder())->diff($current, $wanted, $read->revision, 'Repacked at the warehouse');
if ($change !== null) {
    $requestIri = $client->requestChange($change);
    echo 'Change request: ', $requestIri->value, ' is ', $client->getActionRequest($requestIri)->status->shortName(), "\n";
}

// Errors are typed: the partner's api:Error comes along.
try {
    $client->getLogisticsObject('https://1r.partner.example/logistics-objects/not-ours');
} catch (OneRecordHttpException $e) {
    echo 'Refused with ', $e->status, ': ', $e->error?->title, "\n";
}
