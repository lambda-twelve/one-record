<?php

declare(strict_types=1);

use LambdaTwelve\OneRecord\Api\Permission;
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
use Psr\Http\Message\ServerRequestInterface;

// Who is calling? In production this is JwtAuthenticator (RS256 bearer tokens);
// here a header stands in so the example runs without keys.
$authenticator = new class implements Authenticator {
    public function authenticate(ServerRequestInterface $request): ?Agent
    {
        $agent = $request->getHeaderLine('X-Agent');

        return $agent === '' ? null : new Agent(new Iri($agent));
    }
};
// Any PSR-14 dispatcher; the SDK raises events such as LogisticsObjectCreated.
$dispatcher = new class implements EventDispatcherInterface {
    public function dispatch(object $event): object
    {
        return $event;
    }
};

// Any PSR-17 factory does; nyholm/psr7 is used here.
$factory = new Psr17Factory();
$holder = new Iri('https://1r.example.com/logistics-objects/holder');
$server = new InMemoryServer(
    new ServerConfig('https://1r.example.com', $holder, dataHolderType: Cargo::Company),
    $authenticator,
    new SystemClock(),
    $dispatcher,
    $factory,
    $factory,
);
$server->policy->addInternal($holder);

// The host publishes its own data through the PHP API.
$dataHolder = new DataHolder($server->services);
$piece = $dataHolder->create(
    ObjectBuilder::of(Cargo::Piece)
        ->set(Cargo::goodsDescription, 'Machine parts')
        ->set(Cargo::grossWeight, Values::quantity(190.5, MeasurementUnitCode::KGM))
        ->build(new Iri('https://1r.example.com/logistics-objects/piece-1')),
);

// A partner may read it once the access policy says so.
$partner = new Iri('https://1r.partner.example/logistics-objects/forwarder');
$server->policy->allow($partner, $piece->object->iri, [Permission::GetLogisticsObject]);

// $server->handler is the PSR-15 handler to mount; here it is called directly.
$request = new ServerRequest('GET', 'https://1r.example.com/logistics-objects/piece-1', [
    'Accept' => 'application/ld+json; version=2.3.0',
    'X-Agent' => $partner->value,
]);
$response = $server->handler->handle($request);

echo $response->getStatusCode(), ' ', $response->getHeaderLine('Content-Type'), "\n";
echo 'Type: ', $response->getHeaderLine('Type'), "\n";
echo 'Revision: ', $response->getHeaderLine('Revision'), "\n";
echo $response->getBody(), "\n";
