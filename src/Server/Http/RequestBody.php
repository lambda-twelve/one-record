<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Http;

use LambdaTwelve\OneRecord\JsonLd\Json;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Reads a JSON-LD body within the configured size limit. The limit is checked
 * against Content-Length first and enforced while reading, so a body without
 * a length header cannot exhaust memory either.
 */
final class RequestBody
{
    public function __construct(private readonly int $maxBytes) {}

    /**
     * @return array<string, mixed>
     */
    public function json(ServerRequestInterface $request): array
    {
        $raw = $this->raw($request);
        if (trim($raw) === '') {
            throw HttpException::badRequest('The request body is missing.');
        }
        try {
            return Json::decodeObject($raw);
        } catch (JsonLdException $e) {
            throw HttpException::badRequest($e->getMessage());
        }
    }

    public function raw(ServerRequestInterface $request): string
    {
        $declared = $request->getHeaderLine('Content-Length');
        if ($declared !== '' && is_numeric($declared) && (int) $declared > $this->maxBytes) {
            throw HttpException::payloadTooLarge($this->maxBytes);
        }
        $stream = $request->getBody();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }
        $body = '';
        while (!$stream->eof()) {
            $chunk = $stream->read(8192);
            if ($chunk === '') {
                break;
            }
            $body .= $chunk;
            if (\strlen($body) > $this->maxBytes) {
                throw HttpException::payloadTooLarge($this->maxBytes);
            }
        }

        return $body;
    }
}
