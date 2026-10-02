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
        $accept = $request->getHeaderLine('Accept');
        [$acceptable, $requested] = $this->parseAccept($accept);
        if (!$acceptable) {
            throw HttpException::notAcceptable(\sprintf('This server answers in %s only.', self::JSON_LD));
        }
        $version = $requested ?? $this->config->highestApiVersion();
        if (!$this->config->supports($version)) {
            throw HttpException::notAcceptable(\sprintf('API version %s is not supported; this server speaks %s.', $version->value, implode(', ', array_map(static fn(ApiVersion $v): string => $v->value, $this->config->apiVersions))));
        }

        return new Negotiated($version, $this->language($request), $requested !== null);
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

    /**
     * @return array{bool, ?ApiVersion} whether JSON-LD is acceptable, and the version asked for
     */
    private function parseAccept(string $accept): array
    {
        if (trim($accept) === '') {
            return [true, null];
        }
        // RFC 9110 §12.5.1: the most specific matching range decides, and q=0 there means
        // "not acceptable" even if a wildcard elsewhere would allow it (AR-020).
        $bestSpecificity = -1;
        $bestQ = 0.0;
        $version = null;
        foreach (explode(',', $accept) as $range) {
            [$type, $parameters] = self::splitMediaType($range);
            $q = isset($parameters['q']) && is_numeric($parameters['q']) ? (float) $parameters['q'] : 1.0;
            $specificity = match ($type) {
                self::JSON_LD => 3,
                'application/json' => 2,
                'application/*' => 1,
                '*/*' => 0,
                default => -1,
            };
            if ($specificity < 0 || $specificity < $bestSpecificity || ($specificity === $bestSpecificity && $q <= $bestQ)) {
                continue;
            }
            $bestSpecificity = $specificity;
            $bestQ = $q;
            $version = null;
            if ($q > 0 && isset($parameters['version'])) {
                $version = ApiVersion::tryFromString($parameters['version']);
                if ($version === null) {
                    throw HttpException::notAcceptable(\sprintf('Unknown API version "%s" in Accept.', $parameters['version']));
                }
            }
        }

        return [$bestSpecificity >= 0 && $bestQ > 0, $version];
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
