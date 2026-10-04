<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Change\Change;
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
 * PATCH /logistics-objects/{id}: a Change becomes a pending ChangeRequest for
 * the holder to decide (201 with its Location). The holder's own agents are
 * accepted and applied at once, as the spec says a request by the holder
 * SHOULD be. Access follows PATCH_LOGISTICS_OBJECT (spec question 16).
 */
final class ChangeLogisticsObjectEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $stored = $this->requireObject($parameters['id'], $agent, Action::ChangeLogisticsObject);
        (new ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $change = Change::fromJsonLd($this->services->body->json($request));
        if (!$change->logisticsObject->equals($stored->object->iri)) {
            throw HttpException::badRequest(\sprintf('api:hasLogisticsObject is %s but the request was sent to %s.', $change->logisticsObject->value, $stored->object->iri->value), Api::hasLogisticsObject, 'Invalid resource');
        }
        if ($change->revision > $stored->latestRevision) {
            throw HttpException::unprocessable(\sprintf('The change applies to revision %d but the object is at revision %d.', $change->revision, $stored->latestRevision), Api::hasRevision);
        }

        $requests = new ActionRequests($this->services);
        $created = $requests->create($change, $agent->iri);
        // Unless a creation listener decided it already (R7-001).
        if ($created->status === RequestStatus::Pending && $this->services->policy->decide($agent, Action::DecideActionRequest, $created->iri) === Decision::Allow) {
            $requests->accept($created, $agent->iri);
        }

        return $this->services->responder->empty(201, $negotiated, ['Location' => $created->iri->value, 'Type' => Api::ChangeRequest]);
    }
}
