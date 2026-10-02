<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Http;

use LambdaTwelve\OneRecord\Api\Error;
use RuntimeException;

/**
 * An endpoint's way of answering with an error: the status and the api:Error
 * body the spec requires on every 4xx/5xx, with the 2.3 standard titles.
 */
final class HttpException extends RuntimeException
{
    /**
     * @param list<Error> $errors
     * @param array<string, string> $headers extra response headers (Allow, WWW-Authenticate, ...)
     */
    public function __construct(
        public readonly int $status,
        public readonly array $errors,
        public readonly array $headers = [],
    ) {
        parent::__construct($errors[0]->title ?? 'HTTP ' . $status);
    }

    public static function badRequest(string $message, ?string $property = null, string $title = 'Invalid body request'): self
    {
        return new self(400, [Error::of($title, '400', $message, $property)]);
    }

    public static function invalidQuery(string $message, string $parameter): self
    {
        return new self(400, [Error::of('Invalid query parameter request', '400', $message, $parameter)]);
    }

    public static function unauthenticated(): self
    {
        return new self(401, [Error::of('Not authenticated or expired token', '401', 'The request does not contain valid authentication credentials, or the provided token is expired.')], ['WWW-Authenticate' => 'Bearer']);
    }

    public static function forbidden(?string $resource = null): self
    {
        return new self(403, [Error::of('Not authorized to perform action', '403', 'The authenticated party is not authorized to perform the requested action.', null, $resource)]);
    }

    public static function notFound(string $what, ?string $resource = null): self
    {
        return new self(404, [Error::of('Resource not found', '404', $what . ' could not be found.', null, $resource)]);
    }

    /**
     * @param list<string> $allowed
     */
    public static function methodNotAllowed(array $allowed): self
    {
        return new self(405, [Error::of('Method not allowed', '405', 'The HTTP method is not supported for the requested resource.')], ['Allow' => implode(', ', $allowed)]);
    }

    public static function notAcceptable(string $message): self
    {
        return new self(406, [Error::of('Not acceptable', '406', $message)]);
    }

    public static function conflict(string $message, ?string $resource = null): self
    {
        return new self(409, [Error::of('Identifier conflict', '409', $message, null, $resource)]);
    }

    public static function payloadTooLarge(int $limit): self
    {
        return new self(413, [Error::of('Payload too large', '413', \sprintf('Request bodies are limited to %d bytes.', $limit))]);
    }

    public static function unsupportedMediaType(string $message): self
    {
        return new self(415, [Error::of('Unsupported content type', '415', $message)]);
    }

    /**
     * @param list<Error> $errors
     */
    public static function unprocessable(string $message, ?string $property = null, array $errors = []): self
    {
        return new self(422, $errors === [] ? [Error::of('Unprocessable content', '422', $message, $property)] : $errors);
    }

    public static function internal(string $message = 'The server encountered an unexpected condition that prevented it from fulfilling the request.'): self
    {
        return new self(500, [Error::of('Internal server error', '500', $message)]);
    }
}
