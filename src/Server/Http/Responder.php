<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Http;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\ErrorDocument;
use LambdaTwelve\OneRecord\JsonLd\Json;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Builds responses with the headers the spec puts on every one of them:
 * Content-Type with the negotiated version, Content-Language, and Type for
 * documents. HEAD responses keep the headers (including Content-Length) and
 * drop the body.
 */
final class Responder
{
    public function __construct(
        private readonly ResponseFactoryInterface $responses,
        private readonly StreamFactoryInterface $streams,
    ) {}

    /**
     * @param array<string, mixed> $document
     * @param array<string, string> $headers
     */
    public function jsonLd(int $status, array $document, Negotiated $negotiated, ?string $type = null, array $headers = [], bool $head = false): ResponseInterface
    {
        $json = Json::encode($document);
        $response = $this->responses->createResponse($status)
            ->withHeader('Content-Type', $negotiated->contentType())
            ->withHeader('Content-Language', $negotiated->language)
            ->withHeader('Content-Length', (string) \strlen($json));
        if ($type !== null) {
            $response = $response->withHeader('Type', $type);
        }
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $head ? $response : $response->withBody($this->streams->createStream($json));
    }

    /**
     * @param array<string, string> $headers
     */
    public function empty(int $status, Negotiated $negotiated, array $headers = []): ResponseInterface
    {
        $response = $this->responses->createResponse($status)
            ->withHeader('Content-Type', $negotiated->contentType())
            ->withHeader('Content-Language', $negotiated->language);
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    /**
     * @param list<Error> $errors
     * @param array<string, string> $headers
     */
    public function error(int $status, array $errors, Negotiated $negotiated, array $headers = [], bool $head = false): ResponseInterface
    {
        $error = $errors[0] ?? Error::of('Internal server error', (string) $status);
        if (\count($errors) > 1) {
            $details = [];
            foreach ($errors as $each) {
                foreach ($each->details as $detail) {
                    $details[] = $detail;
                }
            }
            $error = new Error($error->title, $details, $error->severity);
        }

        return $this->jsonLd($status, ErrorDocument::write($error, $negotiated->version, $negotiated->language), $negotiated, Api::Error, $headers, $head);
    }

    public static function httpDate(DateTimeInterface $date): string
    {
        return DateTimeImmutable::createFromInterface($date)->setTimezone(new DateTimeZone('GMT'))->format('D, d M Y H:i:s \G\M\T');
    }
}
