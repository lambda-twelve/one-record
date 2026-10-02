<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
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
 * POST /access-delegations: a request for permissions on this server's
 * objects, for the requester or a third party, becomes a pending
 * AccessDelegationRequest (201). The 2.2 or 2.3 shape is read according to
 * the body's declared version.
 */
final class AccessDelegationsEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $bodyVersion = (new ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $delegation = AccessDelegation::fromJsonLd($this->services->body->json($request), $bodyVersion);
        foreach ($delegation->logisticsObjects as $object) {
            $hidden = $this->services->policy->decide($agent, Action::ReadLogisticsObject, $object) === Decision::Hide;
            if (!$this->services->config->isLocal($object) || $hidden || !$this->services->objects->exists($object)) {
                throw HttpException::badRequest(\sprintf('"%s" is not a logistics object of this server.', $object->value), Api::hasLogisticsObject, 'Invalid resource');
            }
        }
        $created = (new ActionRequests($this->services))->create($delegation, $agent->iri);

        return $this->services->responder->empty(201, $negotiated, ['Location' => $created->iri->value, 'Type' => Api::AccessDelegationRequest]);
    }
}
