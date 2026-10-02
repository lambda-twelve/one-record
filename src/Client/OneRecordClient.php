<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ErrorDocument;
use LambdaTwelve\OneRecord\Api\InvalidDocument;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\ServerInformation;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Api\Verification;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\ModelException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Spec\ApiFeatures;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Throwable;

/**
 * A client for one partner's ONE Record server over PSR-18. It discovers the
 * partner's server information, picks the highest API version both sides
 * support and speaks that version from then on. Responses are parsed into the
 * same model and API documents the server side uses; HTTP errors become
 * OneRecordHttpException with the api:Error the partner sent.
 *
 * Parsing is lenient on purpose: unknown predicates are kept in the graph
 * and 2.3-only properties are optional, so a 2.2 partner is read fine.
 */
final class OneRecordClient
{
    private const string JSON_LD = 'application/ld+json';

    private readonly string $endpoint;

    /** @var list<ApiVersion> */
    private readonly array $ourVersions;

    private ?ApiVersion $version = null;

    private ?ServerInformation $serverInformation = null;

    private readonly LoggerInterface $logger;

    /**
     * @param string $serverEndpoint the partner's ONE Record server endpoint (what its server information calls api:hasServerEndpoint)
     * @param ?list<ApiVersion> $apiVersions the versions this client is willing to speak; all supported ones by default
     * @param int $serverInformationTtl seconds to keep a partner's server information in the cache
     */
    public function __construct(
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requests,
        private readonly StreamFactoryInterface $streams,
        private readonly TokenProvider $tokens,
        string $serverEndpoint,
        private readonly ?CacheInterface $cache = null,
        private readonly ?ClockInterface $clock = null,
        ?LoggerInterface $logger = null,
        ?array $apiVersions = null,
        private readonly int $serverInformationTtl = 3600,
    ) {
        if (preg_match('#^https?://#', $serverEndpoint) !== 1) {
            throw new InvalidArgumentException(\sprintf('The server endpoint must be an absolute http(s) URL, got "%s".', $serverEndpoint));
        }
        $this->endpoint = rtrim($serverEndpoint, '/');
        $this->ourVersions = $apiVersions ?? ApiVersion::allDescending();
        $this->logger = $logger ?? new NullLogger();
    }

    public function endpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Speak this version regardless of what the partner advertises.
     */
    public function withApiVersion(ApiVersion $version): self
    {
        $clone = clone $this;
        $clone->version = $version;

        return $clone;
    }

    /**
     * The partner's server information, cached (PSR-16 when given, else in this object).
     */
    public function serverInformation(bool $refresh = false): ServerInformation
    {
        if (!$refresh && $this->serverInformation !== null) {
            return $this->serverInformation;
        }
        $key = 'one-record.server-information.' . hash('sha256', $this->endpoint);
        if (!$refresh && $this->cache !== null) {
            try {
                $cached = $this->cache->get($key);
                if (\is_string($cached)) {
                    return $this->serverInformation = ServerInformation::fromJsonLd($cached);
                }
            } catch (Throwable) {
                // A cache problem only costs a request.
            }
        }
        // Server information is fetched before a version is negotiated: ask for the highest we speak.
        $response = $this->send('GET', $this->endpoint . '/', null, $this->ourVersions[0]);
        $information = $this->parse(static fn(): ServerInformation => ServerInformation::fromJsonLd(self::body($response)), 'server information');
        if ($this->cache !== null) {
            try {
                $this->cache->set($key, json_encode($information->toJsonLd(), JSON_THROW_ON_ERROR), $this->serverInformationTtl);
            } catch (Throwable) {
            }
        }

        return $this->serverInformation = $information;
    }

    /**
     * The API version used with this partner: the highest both sides support.
     *
     * @throws ClientException when the partner supports none of ours
     */
    public function apiVersion(): ApiVersion
    {
        if ($this->version !== null) {
            return $this->version;
        }
        $information = $this->serverInformation();
        $common = $information->bestCommonApiVersion($this->ourVersions);
        if ($common === null) {
            throw new ClientException(\sprintf('%s supports API %s; this client speaks %s.', $this->endpoint, $information->apiVersions === [] ? 'no listed version' : implode(', ', $information->apiVersions), implode(', ', array_map(static fn(ApiVersion $v): string => $v->value, $this->ourVersions))));
        }
        $this->logger->debug('ONE Record API version negotiated', ['endpoint' => $this->endpoint, 'version' => $common->value]);

        return $this->version = $common;
    }

    // --- Logistics objects -------------------------------------------------

    /**
     * @param ?DateTimeImmutable $at the revision current at that instant (the spec's `?at=` parameter)
     * @param bool $embedded ask the server to embed linked objects it holds
     */
    public function getLogisticsObject(Iri|string $object, ?DateTimeImmutable $at = null, bool $embedded = false): LogisticsObjectResponse
    {
        $iri = self::iri($object);
        $query = [];
        if ($at !== null) {
            $query['at'] = $at->setTimezone(new DateTimeZone('UTC'))->format('Ymd\THis\Z');
        }
        if ($embedded) {
            $query['embedded'] = 'true';
        }
        $response = $this->send('GET', $iri->value . ($query === [] ? '' : '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986)));
        $object = $this->parse(static fn(): LogisticsObject => LogisticsObject::fromJsonLd(self::body($response)), 'logistics object');

        return $this->objectResponse($response, self::withoutRevisionProperties($object));
    }

    public function headLogisticsObject(Iri|string $object): LogisticsObjectResponse
    {
        return $this->objectResponse($this->send('HEAD', self::iri($object)->value), null);
    }

    /**
     * POST /logistics-objects on the partner's server (it must allow it); returns the URI assigned.
     *
     * @param LogisticsObject|array<string, mixed> $object
     */
    public function createLogisticsObject(LogisticsObject|array $object): Iri
    {
        $json = $object instanceof LogisticsObject ? $object->toJsonLd() : $object;

        return self::location($this->send('POST', $this->endpoint . '/logistics-objects', $json));
    }

    /**
     * PATCH a Change; returns the URI of the ChangeRequest the partner created.
     */
    public function requestChange(Change $change): Iri
    {
        return self::location($this->send('PATCH', $change->logisticsObject->value, $change->toJsonLd()));
    }

    /**
     * POST a Verification on the object; returns the URI of the VerificationRequest.
     */
    public function requestVerification(Verification $verification): Iri
    {
        return self::location($this->send('POST', $verification->logisticsObject->value, $verification->toJsonLd($this->apiVersion())));
    }

    public function getAuditTrail(Iri|string $object, ?DateTimeImmutable $updatedFrom = null, ?DateTimeImmutable $updatedTo = null, ?RequestStatus $status = null): AuditTrail
    {
        $query = [];
        foreach (['updated-from' => $updatedFrom, 'updated-to' => $updatedTo] as $name => $value) {
            if ($value !== null) {
                $query[$name] = $value->setTimezone(new DateTimeZone('UTC'))->format('Ymd\THis\Z');
            }
        }
        if ($status !== null) {
            $query['status'] = $status->shortName();
        }
        $response = $this->send('GET', self::iri($object)->value . '/audit-trail' . ($query === [] ? '' : '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986)));

        return $this->parse(function () use ($response): AuditTrail {
            $expanded = JsonLd::expand(self::body($response));
            if (!\in_array(Api::AuditTrail, $expanded->rootTypes(), true)) {
                throw InvalidDocument::because('Invalid resource', 'The body is not an api:AuditTrail.');
            }
            $requests = [];
            foreach (Nodes::nodes($expanded->graph, $expanded->root, Api::hasActionRequest) as $node) {
                $requests[] = ActionRequest::fromJsonLd(new ExpandedDocument($expanded->graph, $node, $expanded->context));
            }

            return new AuditTrail(Nodes::int($expanded->graph, $expanded->root, Api::hasLatestRevision) ?? 0, $requests, self::httpDate($response, 'Last-Modified'));
        }, 'audit trail');
    }

    // --- Logistics events --------------------------------------------------

    /**
     * POST one event on one object; returns the event's URI.
     *
     * @param LogisticsEvent|array<string, mixed> $event
     */
    public function postLogisticsEvent(Iri|string $object, LogisticsEvent|array $event): Iri
    {
        $json = $event instanceof LogisticsEvent ? $event->toJsonLd() : $event;
        unset($json['@id']);

        return self::location($this->send('POST', self::iri($object)->value . '/logistics-events', $json));
    }

    /**
     * One event for several objects. Uses the 2.3 bulk endpoint when the
     * negotiated version has it and the partner serves it; otherwise posts to
     * each object in turn. Either way the answer is one result per object.
     *
     * @param array<string, mixed> $event the event without cargo:eventFor
     * @param list<Iri|string> $objects
     * @return list<BulkEventResult>
     */
    public function postLogisticsEvents(array $event, array $objects): array
    {
        $iris = array_map(self::iri(...), $objects);
        unset($event['@id'], $event['cargo:eventFor'], $event[Cargo::eventFor]);
        if (ApiFeatures::available($this->apiVersion(), ApiFeatures::BULK_LOGISTICS_EVENTS)) {
            try {
                $body = [...$event, 'cargo:eventFor' => array_map(static fn(Iri $i): array => ['@id' => $i->value], $iris)];
                $response = $this->send('POST', $this->endpoint . '/logistics-events', $body, expect: [207]);

                return $this->parse(fn(): array => $this->bulkResults($response, $iris), 'multi-status response');
            } catch (OneRecordHttpException $e) {
                if (!\in_array($e->status, [404, 405], true)) {
                    throw $e;
                }
                $this->logger->info('Bulk events not served; posting per object', ['endpoint' => $this->endpoint, 'status' => $e->status]);
            }
        }
        $results = [];
        foreach ($iris as $iri) {
            try {
                $results[] = new BulkEventResult($iri, 201, $this->postLogisticsEvent($iri, $event), null);
            } catch (OneRecordHttpException $e) {
                $results[] = new BulkEventResult($iri, $e->status, null, $e->error);
            }
        }

        return $results;
    }

    public function getLogisticsEvents(Iri|string $object, ?EventFilter $filter = null): EventList
    {
        $iri = self::iri($object);
        $response = $this->send('GET', $iri->value . '/logistics-events' . ($filter ?? EventFilter::all())->toQueryString());

        return $this->parse(function () use ($response, $iri): EventList {
            $expanded = JsonLd::expand(self::body($response));
            if (!\in_array(Api::Collection, $expanded->rootTypes(), true)) {
                throw InvalidDocument::because('Invalid resource', 'The body is not an api:Collection.');
            }
            $lastModified = self::httpDate($response, 'Last-Modified');
            $events = [];
            foreach (Nodes::nodes($expanded->graph, $expanded->root, Api::hasItem) as $node) {
                if ($node instanceof Iri) {
                    $events[] = $this->event($expanded->graph, $node, $iri, $lastModified);
                }
            }

            return new EventList($events, Nodes::int($expanded->graph, $expanded->root, Api::hasTotalItems) ?? \count($events), $lastModified);
        }, 'event list');
    }

    public function getLogisticsEvent(Iri|string $event): LogisticsEvent
    {
        $iri = self::iri($event);
        $response = $this->send('GET', $iri->value);
        $object = new Iri(preg_replace('#/logistics-events/[^/]+$#', '', $iri->value) ?? $iri->value);

        return $this->parse(fn(): LogisticsEvent => $this->event(JsonLd::expand(self::body($response))->graph, $iri, $object, self::httpDate($response, 'Last-Modified')), 'logistics event');
    }

    // --- Subscriptions, delegations, notifications --------------------------

    /**
     * POST /subscriptions; returns the URI of the SubscriptionRequest.
     */
    public function subscribe(Subscription $subscription): Iri
    {
        return self::location($this->send('POST', $this->endpoint . '/subscriptions', $subscription->toJsonLd()));
    }

    /**
     * GET /subscriptions?topicType&topic: what the partner wants to be told about a topic.
     *
     * @return list<Subscription> none, one, or several (the partner may answer with a Collection)
     */
    public function getSubscriptions(TopicType $topicType, string $topic): array
    {
        $query = http_build_query(['topicType' => $topicType->shortName(), 'topic' => $topic], '', '&', PHP_QUERY_RFC3986);
        $response = $this->send('GET', $this->endpoint . '/subscriptions?' . $query);

        return $this->parse(static function () use ($response): array {
            $expanded = JsonLd::expand(self::body($response));
            if (\in_array(Api::Subscription, $expanded->rootTypes(), true)) {
                return [Subscription::readNode($expanded->graph, $expanded->root)];
            }
            $out = [];
            foreach (Nodes::nodes($expanded->graph, $expanded->root, Api::hasItem) as $node) {
                $out[] = Subscription::readNode($expanded->graph, $node);
            }

            return $out;
        }, 'subscriptions');
    }

    /**
     * POST /access-delegations; returns the URI of the AccessDelegationRequest.
     */
    public function requestAccessDelegation(AccessDelegation $delegation): Iri
    {
        return self::location($this->send('POST', $this->endpoint . '/access-delegations', $delegation->toJsonLd($this->apiVersion())));
    }

    /**
     * POST /notifications on the partner's server (the partner is the subscriber).
     */
    public function sendNotification(Notification $notification): void
    {
        $this->send('POST', $this->endpoint . '/notifications', $notification->toJsonLd(), expect: [204, 200]);
    }

    // --- Action requests ---------------------------------------------------

    public function getActionRequest(Iri|string $request): ActionRequest
    {
        $response = $this->send('GET', self::iri($request)->value);

        return $this->parse(static fn(): ActionRequest => ActionRequest::fromJsonLd(self::body($response)), 'action request');
    }

    /**
     * PATCH /action-requests/{id}?status=…: accept, reject, acknowledge or revoke
     * a request on a partner's server (its policy decides whether we may).
     */
    public function updateActionRequestStatus(Iri|string $request, RequestStatus $status): void
    {
        $this->send('PATCH', self::iri($request)->value . '?status=' . $status->shortName(), null, expect: [204]);
    }

    public function revokeActionRequest(Iri|string $request): void
    {
        $this->send('DELETE', self::iri($request)->value, null, expect: [204]);
    }

    // --- Plumbing ----------------------------------------------------------

    /**
     * @param ?array<string, mixed> $body
     * @param list<int> $expect acceptable status codes; empty means any 2xx
     */
    private function send(string $method, string $url, ?array $body = null, ?ApiVersion $version = null, array $expect = []): ResponseInterface
    {
        $version ??= $this->apiVersion();
        $request = $this->requests->createRequest($method, $url)
            ->withHeader('Accept', self::JSON_LD . '; version=' . $version->value)
            ->withHeader('Authorization', 'Bearer ' . $this->tokens->token($this->endpoint));
        if ($body !== null) {
            $request = $request
                ->withHeader('Content-Type', self::JSON_LD . '; version=' . $version->value)
                ->withBody($this->streams->createStream(json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)));
        }
        try {
            $response = $this->http->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ClientException(\sprintf('%s %s failed: %s', $method, $url, $e->getMessage()), 0, $e);
        }
        $status = $response->getStatusCode();
        if ($status >= 400) {
            $error = null;
            try {
                $error = ErrorDocument::read((string) $response->getBody());
            } catch (Throwable) {
                // Not a ONE Record error body (a proxy page, say); the status still tells the story.
            }
            $this->logger->info('ONE Record request refused', ['method' => $method, 'url' => $url, 'status' => $status, 'title' => $error?->title]);

            throw new OneRecordHttpException($status, $error, $response, $method, $url);
        }
        if ($status < 200 || $status >= 300 || ($expect !== [] && !\in_array($status, $expect, true))) {
            throw new ClientException(\sprintf('%s %s answered %d where %s was expected.', $method, $url, $status, $expect === [] ? 'a 2xx' : implode(' or ', $expect)));
        }

        return $response;
    }

    /**
     * @template T
     * @param callable(): T $parse
     * @return T
     */
    private function parse(callable $parse, string $what)
    {
        try {
            return $parse();
        } catch (JsonLdException|InvalidDocument|ModelException $e) {
            throw new ClientException(\sprintf('%s answered with a %s this client cannot read: %s', $this->endpoint, $what, $e->getMessage()), 0, $e);
        }
    }

    private function objectResponse(ResponseInterface $response, ?LogisticsObject $object): LogisticsObjectResponse
    {
        $revision = (int) $response->getHeaderLine('Revision');
        $latest = (int) $response->getHeaderLine('Latest-Revision');
        $type = $response->getHeaderLine('Type');
        $version = ApiVersion::tryFromString(self::versionParameter($response->getHeaderLine('Content-Type')) ?? '') ?? $this->apiVersion();

        return new LogisticsObjectResponse($object, $revision > 0 ? $revision : $latest, $latest > 0 ? $latest : $revision, self::httpDate($response, 'Last-Modified'), $type === '' ? $object?->mostSpecificType() : $type, $version);
    }

    /**
     * @param list<Iri> $objects
     * @return list<BulkEventResult>
     */
    private function bulkResults(ResponseInterface $response, array $objects): array
    {
        $expanded = JsonLd::expand(self::body($response));
        $graph = $expanded->graph;
        $results = [];
        foreach (Nodes::nodes($graph, $expanded->root, Api::hasCreationResult) as $node) {
            $object = Nodes::iri($graph, $node, Api::hasLogisticsObject);
            if ($object === null) {
                continue;
            }
            $errorNode = Nodes::node($graph, $node, Api::hasError);
            $results[] = new BulkEventResult(
                $object,
                Nodes::int($graph, $node, Api::hasHTTPStatus) ?? 0,
                Nodes::iri($graph, $node, Api::hasLogisticsEvent),
                $errorNode === null ? null : ErrorDocument::readNode($graph, $errorNode),
            );
        }

        return $results;
    }

    private function event(Graph $graph, Iri $iri, Iri $object, ?DateTimeImmutable $fallbackCreated): LogisticsEvent
    {
        $subgraph = new Graph();
        self::collect($graph, $iri, $subgraph, []);
        $for = Nodes::iri($subgraph, $iri, Cargo::eventFor);
        $created = Nodes::dateTime($subgraph, $iri, Cargo::creationDate) ?? $fallbackCreated ?? ($this->clock?->now() ?? new DateTimeImmutable('now', new DateTimeZone('UTC')));

        return new LogisticsEvent($iri, $for ?? $object, $subgraph, $created);
    }

    /**
     * @param array<string, true> $seen
     */
    private static function collect(Graph $graph, Iri|BlankNode $node, Graph $into, array $seen): void
    {
        $seen[$node->toNTriples()] = true;
        foreach ($graph->about($node) as $triple) {
            $into->add(new Triple($triple->subject, $triple->predicate, $triple->object));
            $object = $triple->object;
            if (($object instanceof Iri || $object instanceof BlankNode) && !isset($seen[$object->toNTriples()]) && ($object instanceof BlankNode || LogisticsObject::isEmbeddedId($object))) {
                self::collect($graph, $object, $into, $seen);
            }
        }
    }

    /**
     * The revision properties are response metadata the server adds to a body
     * (and the response object carries them); without them the object compares
     * equal to what the holder stored and can be diffed or republished as is.
     */
    private static function withoutRevisionProperties(LogisticsObject $object): LogisticsObject
    {
        $graph = new Graph();
        foreach ($object->graph as $triple) {
            if ($triple->subject->equals($object->iri) && \in_array($triple->predicate->value, [Api::hasRevision, Api::hasLatestRevision], true)) {
                continue;
            }
            $graph->add($triple);
        }

        return $object->withGraph($graph);
    }

    private static function iri(Iri|string $value): Iri
    {
        return $value instanceof Iri ? $value : new Iri($value);
    }

    private static function body(ResponseInterface $response): string
    {
        return (string) $response->getBody();
    }

    private static function location(ResponseInterface $response): Iri
    {
        $location = $response->getHeaderLine('Location');
        if ($location === '') {
            throw new ClientException(\sprintf('The server answered %d without a Location header.', $response->getStatusCode()));
        }

        return new Iri($location);
    }

    private static function httpDate(ResponseInterface $response, string $header): ?DateTimeImmutable
    {
        $value = $response->getHeaderLine($header);
        if ($value === '') {
            return null;
        }
        $parsed = DateTimeImmutable::createFromFormat('D, d M Y H:i:s \G\M\T', $value, new DateTimeZone('UTC'));

        return $parsed === false ? null : $parsed;
    }

    private static function versionParameter(string $contentType): ?string
    {
        return preg_match('/;\s*version\s*=\s*"?([0-9.]+)"?/i', $contentType, $m) === 1 ? $m[1] : null;
    }
}
