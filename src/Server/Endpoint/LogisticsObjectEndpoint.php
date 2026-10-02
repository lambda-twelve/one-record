<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * GET and HEAD /logistics-objects/{id}: the latest version, a historical one
 * with ?at=, with linked objects embedded on ?embedded=true.
 */
final class LogisticsObjectEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $query = self::query($request);
        $atParameter = isset($query['at']) && trim($query['at']) !== '' ? trim($query['at']) : null;
        $at = null;
        if ($atParameter !== null) {
            $at = self::parseAt($atParameter);
            if ($at > $this->services->clock->now()) {
                throw HttpException::invalidQuery('The at parameter must be in the past.', 'at');
            }
        }
        $embedded = isset($query['embedded']) && \in_array(strtolower($query['embedded']), ['true', '1'], true);

        $stored = $this->requireObject($parameters['id'], $agent, Action::ReadLogisticsObject, at: $at);
        $location = $stored->object->iri->value . ($atParameter !== null ? '?at=' . $atParameter : '');
        $document = $this->writeObject($stored, $agent, $embedded, $atParameter);

        return $this->services->responder->jsonLd(200, $document, $negotiated, $this->objectHeaders($stored, $location)['Type'] ?? null, $this->objectHeaders($stored, $location), self::isHead($request));
    }
}
