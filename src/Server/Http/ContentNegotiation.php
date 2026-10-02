<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Http;

use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The spec's versioning through content negotiation: the API version rides in
 * the `version` parameter of Accept (and of Content-Type on bodies) and the
 * server answers in the version it picked. No version means the highest the
 * server supports. Only application/ld+json is served or read.
 */
final class ContentNegotiation
{
    public const string JSON_LD = 'application/ld+json';

    public function __construct(private readonly ServerConfig $config) {}

    public function negotiate(ServerRequestInterface $request): Negotiated
    {
        $accept = trim($request->getHeaderLine('Accept'));
        if ($accept === '') {
            return new Negotiated($this->config->highestApiVersion(), $this->language($request), false);
        }
        // RFC 9110 §12.5.1 applied per representation: for each version this server serves, the
        // most specific range that matches it (type, then version parameter) decides its quality;
        // then the best available representation wins. A range naming an unknown version simply
        // matches nothing, so it cannot veto a representation another range accepts (R2-007).
        $ranges = [];
        $unknownVersion = null;
        foreach (explode(',', $accept) as $range) {
            [$type, $parameters] = self::splitMediaType($range);
            $specificity = match ($type) {
                self::JSON_LD => 3,
                'application/json' => 2,
                'application/*' => 1,
                '*/*' => 0,
                default => -1,
            };
            if ($specificity < 0) {
                continue;
            }
            $version = null;
            if (isset($parameters['version'])) {
                $version = ApiVersion::tryFromString($parameters['version']);
                if ($version === null || !$this->config->supports($version)) {
                    $unknownVersion ??= $parameters['version'];
                    continue;
                }
            }
            $ranges[] = ['specificity' => $specificity, 'version' => $version, 'q' => isset($parameters['q']) && is_numeric($parameters['q']) ? (float) $parameters['q'] : 1.0];
        }
        if ($ranges === [] && $unknownVersion === null) {
            throw HttpException::notAcceptable(\sprintf('This server answers in %s only.', self::JSON_LD));
        }
        $best = null;
        foreach ($this->config->apiVersions as $candidate) {
            $match = null;
            foreach ($ranges as $range) {
                if ($range['version'] !== null && $range['version'] !== $candidate) {
                    continue;
                }
                $score = $range['specificity'] * 2 + ($range['version'] === null ? 0 : 1);
                if ($match === null || $score > $match['score']) {
                    $match = ['score' => $score, 'q' => $range['q'], 'explicit' => $range['version'] !== null];
                }
            }
            if ($match === null || $match['q'] <= 0) {
                continue;
            }
            // Versions are listed highest first, so on equal quality the higher one stays.
            if ($best === null || $match['q'] > $best['q']) {
                $best = ['version' => $candidate, 'q' => $match['q'], 'explicit' => $match['explicit']];
            }
        }
        if ($best === null) {
            throw $unknownVersion !== null
                ? HttpException::notAcceptable(\sprintf('API version %s is not supported; this server speaks %s.', $unknownVersion, implode(', ', array_map(static fn(ApiVersion $v): string => $v->value, $this->config->apiVersions))))
                : HttpException::notAcceptable(\sprintf('This server answers in %s only.', self::JSON_LD));
        }

        return new Negotiated($best['version'], $this->language($request), $best['explicit']);
    }

    /**
     * Checks the Content-Type of a request that carries a body. A version
     * parameter there is read too, so a client declaring a 2.2 body on a 2.3
     * server has its body read with 2.2 rules.
     */
    public function bodyVersion(ServerRequestInterface $request, Negotiated $negotiated): ApiVersion
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if ($contentType === '') {
            throw HttpException::unsupportedMediaType('A Content-Type header of ' . self::JSON_LD . ' is required.');
        }
        [$type, $parameters] = self::splitMediaType($contentType);
        if ($type !== self::JSON_LD && $type !== 'application/json') {
            throw HttpException::unsupportedMediaType(\sprintf('Bodies must be %s, not %s.', self::JSON_LD, $type));
        }
        $version = isset($parameters['version']) ? ApiVersion::tryFromString($parameters['version']) : null;
        if (isset($parameters['version']) && $version === null) {
            throw HttpException::unsupportedMediaType(\sprintf('Unknown API version "%s" in Content-Type.', $parameters['version']));
        }
        if ($version !== null && !\in_array($version, $this->config->apiVersions, true)) {
            // A version this server was configured not to serve is not accepted on input either (AR-020).
            throw HttpException::unsupportedMediaType(\sprintf('API version %s is not served here; this server speaks %s.', $version->value, implode(', ', array_map(static fn(ApiVersion $v): string => $v->value, $this->config->apiVersions))));
        }

        return $version ?? $negotiated->version;
    }


    private function language(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine('Accept-Language');
        if (trim($header) === '') {
            return 'en-US';
        }
        foreach (explode(',', $header) as $range) {
            [$tag] = self::splitMediaType($range);
            foreach ($this->config->languages as $supported) {
                if (strcasecmp($tag, $supported) === 0 || strcasecmp($tag, explode('-', $supported)[0]) === 0) {
                    return $supported;
                }
            }
        }

        return 'en-US';
    }

    /**
     * @return array{string, array<string, string>}
     */
    public static function splitMediaType(string $value): array
    {
        $parts = array_map(trim(...), explode(';', $value));
        $type = strtolower((string) array_shift($parts));
        $parameters = [];
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            [$name, $parameterValue] = array_pad(explode('=', $part, 2), 2, '');
            $parameters[strtolower(trim($name))] = trim(trim($parameterValue), '"');
        }

        return [$type, $parameters];
    }
}
