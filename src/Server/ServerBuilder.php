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

    public static function build(Services $services): OneRecordServer
    {
        $router = new Router();
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

        return new OneRecordServer($services->config, $router, $services->authenticator, $services->responder, $services->logger);
    }
}
