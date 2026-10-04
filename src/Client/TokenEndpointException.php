<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

/**
 * The token endpoint answered, but not with a token. The status and the
 * OAuth error code (RFC 6749 §5.2, when the body carried one) are here so a
 * caller can tell refused credentials (400 invalid_client, 401) from an
 * outage (503) without parsing a sentence; DeliveryVerdict does exactly that.
 */
final class TokenEndpointException extends ClientException
{
    public function __construct(
        public readonly int $status,
        public readonly ?string $error,
        string $tokenUrl,
    ) {
        parent::__construct(\sprintf('Token endpoint %s answered %d%s.', $tokenUrl, $status, $error !== null ? ' (' . $error . ')' : ''));
    }
}
