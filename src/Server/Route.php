<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Spec\ApiVersion;

/**
 * One row of the server's route table, for hosts whose framework wants a
 * named route per endpoint (access control, logging, alteration). The
 * pattern uses `{id}`-style placeholders and is relative to the base path;
 * `methods` lists every method the SDK answers so the framework passes all
 * of them through and the SDK, not the framework, answers 405 with the
 * spec's error body and Allow header.
 */
final readonly class Route
{
    /**
     * @param non-empty-list<string> $methods
     */
    public function __construct(
        public string $name,
        public array $methods,
        public string $pattern,
        public ApiVersion $since = ApiVersion::V2_2_0,
    ) {}
}
