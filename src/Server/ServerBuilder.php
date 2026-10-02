<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Server\Endpoint\AccessDelegationsEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\ActionRequestEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\AuditTrailEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\BulkLogisticsEventsEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\ChangeLogisticsObjectEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\CreateLogisticsObjectEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\LogisticsEventEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\LogisticsEventsEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\LogisticsObjectEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\NotificationsEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\ServerInformationEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\SubscriptionsEndpoint;
use LambdaTwelve\OneRecord\Server\Endpoint\VerificationEndpoint;
use LambdaTwelve\OneRecord\Spec\ApiVersion;

/**
 * Wires every endpoint of the API onto a router and returns the handler. A
 * host calls this once with its Services; the in-memory server for tests and
 * bin/serve calls it too.
 */
final class ServerBuilder
{
    private function __construct() {}

    /**
     * The route table: every endpoint the server answers, with every method it
     * handles. Hosts whose framework wants named routes iterate this instead of
     * copying the patterns; a new endpoint in the SDK then needs no host change.
     *
     * @return list<Route>
     */
    public static function routes(bool $bulkLogisticsEvents = false): array
    {
        $routes = [
            new Route('server-information', ['GET', 'HEAD'], '/'),
            new Route('logistics-objects.create', ['POST'], '/logistics-objects'),
            new Route('logistics-object', ['GET', 'HEAD', 'PATCH', 'POST'], '/logistics-objects/{id}'),
            new Route('logistics-object.audit-trail', ['GET', 'HEAD'], '/logistics-objects/{id}/audit-trail'),
            new Route('logistics-object.events', ['POST', 'GET', 'HEAD'], '/logistics-objects/{id}/logistics-events'),
            new Route('logistics-object.event', ['GET', 'HEAD'], '/logistics-objects/{id}/logistics-events/{eventId}'),
            new Route('notifications', ['POST'], '/notifications'),
            new Route('subscriptions', ['GET', 'HEAD', 'POST'], '/subscriptions'),
            new Route('access-delegations', ['POST'], '/access-delegations'),
            new Route('action-request', ['GET', 'HEAD', 'PATCH', 'DELETE'], '/action-requests/{id}'),
        ];
        if ($bulkLogisticsEvents) {
            $routes[] = new Route('logistics-events.bulk', ['POST'], '/logistics-events', ApiVersion::V2_3_0);
        }

        return $routes;
    }

    public static function build(Services $services): OneRecordServer
    {
        $router = new Router();
        // One Route may be served by several endpoint classes (GET, PATCH and POST on an object differ).
        $router->add(['GET', 'HEAD'], '/', new ServerInformationEndpoint($services));
        $router->add(['POST'], '/logistics-objects', new CreateLogisticsObjectEndpoint($services));
        $router->add(['GET', 'HEAD'], '/logistics-objects/{id}', new LogisticsObjectEndpoint($services));
        $router->add(['PATCH'], '/logistics-objects/{id}', new ChangeLogisticsObjectEndpoint($services));
        $router->add(['POST'], '/logistics-objects/{id}', new VerificationEndpoint($services));
        $router->add(['GET', 'HEAD'], '/logistics-objects/{id}/audit-trail', new AuditTrailEndpoint($services));
        $router->add(['POST', 'GET', 'HEAD'], '/logistics-objects/{id}/logistics-events', new LogisticsEventsEndpoint($services));
        $router->add(['GET', 'HEAD'], '/logistics-objects/{id}/logistics-events/{eventId}', new LogisticsEventEndpoint($services));
        $router->add(['POST'], '/notifications', new NotificationsEndpoint($services));
        $router->add(['GET', 'HEAD', 'POST'], '/subscriptions', new SubscriptionsEndpoint($services));
        $router->add(['POST'], '/access-delegations', new AccessDelegationsEndpoint($services));
        $router->add(['GET', 'HEAD', 'PATCH', 'DELETE'], '/action-requests/{id}', new ActionRequestEndpoint($services));
        if ($services->config->bulkLogisticsEvents) {
            $router->add(['POST'], '/logistics-events', new BulkLogisticsEventsEndpoint($services), ApiVersion::V2_3_0);
        }

        return new OneRecordServer($services->config, $router, $services->authenticator, $services->responder, $services->logger, $services->unitOfWork);
    }
}
