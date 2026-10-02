<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use LambdaTwelve\OneRecord\Api\Error;
use Psr\Http\Message\ResponseInterface;

/**
 * A 4xx or 5xx answer from a ONE Record server, with the api:Error document
 * parsed when the server sent one.
 */
final class OneRecordHttpException extends ClientException
{
    public function __construct(
        public readonly int $status,
        public readonly ?Error $error,
        public readonly ResponseInterface $response,
        string $method,
        string $url,
    ) {
        $detail = $error?->details[0]->message ?? null;
        parent::__construct(\sprintf(
            '%s %s answered %d%s%s',
            $method,
            $url,
            $status,
            $error !== null ? ': ' . $error->title : '',
            $detail !== null ? ' (' . $detail . ')' : '',
        ));
    }

    public function isNotFound(): bool
    {
        return $this->status === 404;
    }

    public function isForbidden(): bool
    {
        return $this->status === 403;
    }

    public function isConflict(): bool
    {
        return $this->status === 409;
    }
}
