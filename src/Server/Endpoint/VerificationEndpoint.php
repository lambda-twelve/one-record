<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Api\Verification;
use LambdaTwelve\OneRecord\Server\ActionRequests;
use LambdaTwelve\OneRecord\Server\Http\ContentNegotiation;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * POST /logistics-objects/{id} with an api:Verification: a third party flags
 * a problem. Authentication only, per spec; a policy that hides the object
 * still hides it here so the endpoint cannot be used to probe existence.
 */
final class VerificationEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $iri = $this->services->config->logisticsObjectIri($parameters['id']);
        if ($this->services->policy->decide($agent, Action::ReadLogisticsObject, $iri) === Decision::Hide) {
            throw HttpException::notFound('Logistics Object', $iri->value);
        }
        $stored = $this->services->objects->latest($iri) ?? throw HttpException::notFound('Logistics Object', $iri->value);
        (new ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $verification = Verification::fromJsonLd($this->services->body->json($request));
        if (!$verification->logisticsObject->equals($stored->object->iri)) {
            throw HttpException::badRequest(\sprintf('api:hasLogisticsObject is %s but the request was sent to %s.', $verification->logisticsObject->value, $stored->object->iri->value), Api::hasLogisticsObject, 'Invalid resource');
        }
        if ($verification->revision !== null && $verification->revision > $stored->latestRevision) {
            throw HttpException::unprocessable(\sprintf('Revision %d does not exist yet; the object is at revision %d.', $verification->revision, $stored->latestRevision), Api::hasRevision);
        }
        $created = (new ActionRequests($this->services))->create($verification, $agent->iri);

        return $this->services->responder->empty(201, $negotiated, ['Location' => $created->iri->value, 'Type' => Api::VerificationRequest]);
    }
}
