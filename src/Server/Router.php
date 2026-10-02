<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Server\Endpoint\Endpoint;
use LambdaTwelve\OneRecord\Spec\ApiVersion;

/**
 * Maps method and path (below the base path) to an endpoint. Routes declare
 * the API version they appeared in, so an endpoint added in 2.3 does not exist
 * for a request negotiated at 2.2. Path parameters are URL segments without
 * slashes, which is also what keeps object ids from containing one.
 */
final class Router
{
    /** @var list<array{methods: list<string>, pattern: string, regex: string, endpoint: Endpoint, since: ApiVersion}> */
    private array $routes = [];

    /**
     * @param list<string> $methods
     * @param string $pattern e.g. /logistics-objects/{id}/logistics-events
     */
    public function add(array $methods, string $pattern, Endpoint $endpoint, ApiVersion $since = ApiVersion::V2_2_0): void
    {
        // Quote the literal parts only; the placeholders become named groups.
        $regex = '#^' . preg_replace_callback(
            '/\{(\w+)\}|[^{]+/',
            static fn(array $m): string => ($m[1] ?? '') !== '' ? '(?P<' . $m[1] . '>[^/]+)' : preg_quote($m[0], '#'),
            $pattern,
        ) . '/?$#';
        $this->routes[] = ['methods' => array_map(strtoupper(...), $methods), 'pattern' => $pattern, 'regex' => $regex, 'endpoint' => $endpoint, 'since' => $since];
    }

    /**
     * @return array{endpoint: Endpoint, parameters: array<string, string>, since: ApiVersion}|array{allowed: list<string>}|null
     *                                                                                                                           a match, a 405 hint listing the methods the path does support, or null for an unknown path
     */
    public function match(string $method, string $path): ?array
    {
        $method = strtoupper($method);
        $allowed = [];
        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches) !== 1) {
                continue;
            }
            foreach ($route['methods'] as $allowedMethod) {
                $allowed[$allowedMethod] = true;
            }
            if (!\in_array($method, $route['methods'], true)) {
                continue;
            }
            $parameters = [];
            foreach ($matches as $key => $value) {
                if (\is_string($key)) {
                    $parameters[$key] = rawurldecode($value);
                }
            }

            return ['endpoint' => $route['endpoint'], 'parameters' => $parameters, 'since' => $route['since']];
        }
        if ($allowed !== []) {
            $methods = array_keys($allowed);
            sort($methods, SORT_STRING);

            return ['allowed' => $methods];
        }

        return null;
    }
}
