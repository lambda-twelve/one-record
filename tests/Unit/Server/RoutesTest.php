<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Server;

use LambdaTwelve\OneRecord\Server\Route;
use LambdaTwelve\OneRecord\Server\ServerBuilder;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use PHPUnit\Framework\TestCase;

/**
 * The route table hosts read must match what the router actually serves.
 */
final class RoutesTest extends TestCase
{
    public function testTheTableListsEveryEndpointOnceWithAllItsMethods(): void
    {
        $routes = ServerBuilder::routes();
        self::assertCount(10, $routes);
        $names = array_map(static fn(Route $r): string => $r->name, $routes);
        self::assertSame($names, array_values(array_unique($names)), 'names are unique');
        $patterns = array_map(static fn(Route $r): string => $r->pattern, $routes);
        self::assertSame($patterns, array_values(array_unique($patterns)), 'one row per pattern, methods merged');
        $object = array_values(array_filter($routes, static fn(Route $r): bool => $r->pattern === '/logistics-objects/{id}'))[0];
        self::assertSame(['GET', 'HEAD', 'PATCH', 'POST'], $object->methods, 'read, change request and verification on one pattern');
        foreach ($routes as $route) {
            self::assertSame(ApiVersion::V2_2_0, $route->since);
        }
    }

    public function testBulkEventsAppearOnlyWhenEnabled(): void
    {
        $bulk = array_values(array_filter(ServerBuilder::routes(bulkLogisticsEvents: true), static fn(Route $r): bool => $r->pattern === '/logistics-events'));
        self::assertCount(1, $bulk);
        self::assertSame(ApiVersion::V2_3_0, $bulk[0]->since);
        self::assertSame(['POST'], $bulk[0]->methods);
    }
}
