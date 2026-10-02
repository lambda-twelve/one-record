<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Api\ServerInformation;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * GET /: api:ServerInformation. Authentication only; no access control, as the
 * spec's security table says.
 */
final class ServerInformationEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $config = $this->services->config;
        $holderType = $config->dataHolderType;
        if ($holderType === null) {
            $holder = $this->services->objects->latest($config->dataHolder);
            $holderType = $holder?->object->mostSpecificType($this->services->vocabulary);
        }
        $information = ServerInformation::for(new Iri($config->endpoint()), $config->dataHolder, $config->apiVersions, $config->dataModelVersions, $config->languages, $holderType);
        // Server information changes only with configuration; "now" is the honest Last-Modified without a deployment timestamp.
        $headers = ['Last-Modified' => Responder::httpDate($this->services->clock->now())];

        return $this->services->responder->jsonLd(200, $information->toJsonLd(), $negotiated, Api::ServerInformation, $headers, self::isHead($request));
    }
}
