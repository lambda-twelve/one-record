<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Http;

use LambdaTwelve\OneRecord\Spec\ApiVersion;

/**
 * What one request negotiated: the API version to answer in and the language.
 */
final readonly class Negotiated
{
    public function __construct(
        public ApiVersion $version,
        public string $language,
        public bool $versionRequested,
    ) {}

    public function contentType(): string
    {
        return 'application/ld+json; version=' . $this->version->value;
    }
}
