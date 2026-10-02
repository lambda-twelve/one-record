<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\AuditTrailQuery;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * GET /logistics-objects/{id}/audit-trail: every change and verification
 * request about the object, with the latest revision. Access follows the
 * object unless the host's policy separates them (Action::ReadAuditTrail).
 */
final class AuditTrailEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $stored = $this->requireObject($parameters['id'], $agent, Action::ReadAuditTrail);
        $query = self::query($request);
        $status = null;
        if (isset($query['status']) && trim($query['status']) !== '') {
            $status = RequestStatus::tryFromString($query['status']) ?? throw HttpException::invalidQuery(\sprintf('"%s" is not a request status.', $query['status']), 'status');
        }
        $trailQuery = new AuditTrailQuery(
            isset($query['updated-from']) && trim($query['updated-from']) !== '' ? self::parseAt($query['updated-from']) : null,
            isset($query['updated-to']) && trim($query['updated-to']) !== '' ? self::parseAt($query['updated-to']) : null,
            $status,
        );
        $requests = $this->services->actionRequests->auditTrail($stored->object->iri, $trailQuery);

        $document = [
            '@context' => [...Nodes::context(), 'api:hasDatatype' => ['@type' => 'xsd:anyURI'], 'api:p' => ['@type' => 'xsd:anyURI'], 'api:hasProperty' => ['@type' => 'xsd:anyURI'], 'api:hasResource' => ['@type' => 'xsd:anyURI']],
            '@id' => $stored->object->iri->value . '/audit-trail',
            '@type' => 'api:AuditTrail',
            'api:hasLatestRevision' => Nodes::positiveInteger($stored->latestRevision),
        ];
        $nodes = array_map(fn(ActionRequest $r): array => $r->node($negotiated->version), $requests);
        if ($nodes !== []) {
            $document['api:hasActionRequest'] = $nodes;
        }
        $lastModified = $stored->lastModified;
        foreach ($requests as $actionRequest) {
            $lastModified = max($lastModified, $actionRequest->lastModified());
        }

        return $this->services->responder->jsonLd(200, $document, $negotiated, Api::AuditTrail, ['Last-Modified' => Responder::httpDate($lastModified)], self::isHead($request));
    }
}
