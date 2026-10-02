<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Server\ActionRequests;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\IllegalTransition;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use LambdaTwelve\OneRecord\Spec\ApiFeatures;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * /action-requests/{id}: GET and HEAD for the requestor, the parties it
 * concerns and the holder; PATCH ?status= for the holder to decide
 * ("internal only" per spec, so the policy must allow it); DELETE to revoke,
 * by the requestor or the holder.
 */
final class ActionRequestEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $iri = $this->services->config->actionRequestIri($parameters['id']);
        $actionRequest = $this->services->actionRequests->get($iri);
        $method = strtoupper($request->getMethod());

        if ($method === 'PATCH') {
            $this->decide($agent, Action::DecideActionRequest, $iri);
            $actionRequest ??= throw HttpException::notFound('Action Request', $iri->value);

            return $this->update($request, $agent, $negotiated, $actionRequest);
        }

        if ($actionRequest === null) {
            throw HttpException::notFound('Action Request', $iri->value);
        }
        if ($method === 'DELETE') {
            // Being a party lets you read a request; revoking it is the requestor's right, or the
            // policy's call. A delegate of a shared delegation must not be able to cut off the others (AR-028).
            // The spec tells a subscriber to revoke its SubscriptionRequest to unsubscribe, even when a
            // third party created it (spec question 30); a delegate of a shared delegation gets no such right.
            $subscriber = $actionRequest->payload instanceof Subscription && $agent->is($actionRequest->payload->subscriber);
            if (!$agent->is($actionRequest->requestedBy) && !$subscriber) {
                $this->decide($agent, Action::RevokeActionRequest, $iri);
            }

            return $this->transition($actionRequest, RequestStatus::Revoked, $agent, $negotiated);
        }

        if (!$this->isParty($agent, $actionRequest)) {
            $decision = $this->services->policy->decide($agent, Action::ReadActionRequest, $iri);
            if ($decision !== Decision::Allow) {
                // Someone else's request: say nothing about it, whichever way the policy denies.
                throw HttpException::notFound('Action Request', $iri->value);
            }
        }
        $headers = ['Location' => $iri->value, 'Last-Modified' => Responder::httpDate($actionRequest->lastModified())];

        return $this->services->responder->jsonLd(200, $actionRequest->toJsonLd($negotiated->version), $negotiated, $actionRequest->type->value, $headers, self::isHead($request));
    }

    private function update(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, ActionRequest $actionRequest): ResponseInterface
    {
        $query = self::query($request);
        $value = $query['status'] ?? '';
        $status = $value === '' ? null : RequestStatus::tryFromString($value);
        if ($status === null) {
            throw HttpException::invalidQuery('status must be REQUEST_ACCEPTED, REQUEST_REJECTED, REQUEST_REVOKED or REQUEST_ACKNOWLEDGED (as a short name or an api: IRI).', 'status');
        }
        if (!\in_array($status, [RequestStatus::Accepted, RequestStatus::Rejected, RequestStatus::Revoked, RequestStatus::Acknowledged], true)) {
            throw HttpException::invalidQuery(\sprintf('A request cannot be set to %s through the API.', $status->shortName()), 'status');
        }

        return $this->transition($actionRequest, $status, $agent, $negotiated);
    }

    private function transition(ActionRequest $actionRequest, RequestStatus $status, Agent $agent, Negotiated $negotiated): ResponseInterface
    {
        $requests = new ActionRequests($this->services);
        try {
            match ($status) {
                RequestStatus::Accepted => $requests->accept($actionRequest, $agent->iri),
                RequestStatus::Rejected => $requests->reject($actionRequest, $agent->iri),
                RequestStatus::Acknowledged => $requests->acknowledge($actionRequest, $agent->iri),
                RequestStatus::Revoked => $requests->revoke($actionRequest, $agent->iri),
                default => throw HttpException::invalidQuery('Unsupported status.', 'status'),
            };
        } catch (IllegalTransition $e) {
            // 2.3 specifies 422 for an impossible revocation; 2.2 says nothing, and 400 was the common reading.
            throw ApiFeatures::available($negotiated->version, ApiFeatures::REVOCATION_422)
                ? HttpException::unprocessable($e->getMessage(), null)
                : HttpException::badRequest($e->getMessage(), null, 'Invalid resource');
        } catch (StoreException $e) {
            if ($e->kind !== StoreException::STATUS_CONFLICT) {
                throw $e;
            }
            // Another worker decided first: the unit of work has unwound, the client retries against the new state (R2-012).
            throw HttpException::conflict($e->getMessage(), $actionRequest->iri->value);
        }

        return $this->services->responder->empty(204, $negotiated, ['Location' => $actionRequest->iri->value, 'Type' => $actionRequest->type->value]);
    }

    /**
     * The requestor, and the organisations a request is about (the delegate of
     * an access delegation, the subscriber of a subscription), may always see it.
     */
    private function isParty(Agent $agent, ActionRequest $actionRequest): bool
    {
        if ($agent->is($actionRequest->requestedBy)) {
            return true;
        }
        $payload = $actionRequest->payload;
        if ($payload instanceof Subscription && $agent->is($payload->subscriber)) {
            return true;
        }
        if ($payload instanceof AccessDelegation) {
            foreach ($payload->delegates as $delegate) {
                if ($agent->is($delegate)) {
                    return true;
                }
            }
        }

        return false;
    }
}
