<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Api\Collection;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Rdf\Iri;
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
 * /subscriptions. GET ?topicType&topic: a publisher asks what subscription
 * this host wants for an object or a type (answered from the store's offers).
 * POST: a subscriber asks to be notified; becomes a pending
 * SubscriptionRequest the holder decides (201 with its Location).
 */
final class SubscriptionsEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        return strtoupper($request->getMethod()) === 'POST'
            ? $this->subscribe($request, $agent, $negotiated)
            : $this->offers($request, $negotiated);
    }

    private function offers(ServerRequestInterface $request, Negotiated $negotiated): ResponseInterface
    {
        $query = self::query($request);
        $topicType = isset($query['topicType']) ? TopicType::tryFromString($query['topicType']) : null;
        if ($topicType === null) {
            throw HttpException::invalidQuery('topicType must be LOGISTICS_OBJECT_TYPE or LOGISTICS_OBJECT_IDENTIFIER.', 'topicType');
        }
        $topic = trim($query['topic'] ?? '');
        if ($topic === '') {
            throw HttpException::invalidQuery('topic is required.', 'topic');
        }
        $this->validateTopic($topicType, $topic);

        $offers = $this->services->subscriptions->offered($topicType, $topic);
        if (\count($offers) === 1) {
            return $this->services->responder->jsonLd(200, $offers[0]->toJsonLd(), $negotiated, Api::Subscription, [], self::isHead($request));
        }
        $items = array_map(static fn(Subscription $s): array => $s->node(), $offers);
        $collection = Collection::write(new Iri($this->services->config->endpoint() . '/subscriptions'), $items);

        return $this->services->responder->jsonLd(200, $collection, $negotiated, Api::Collection, [], self::isHead($request));
    }

    private function subscribe(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated): ResponseInterface
    {
        (new ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $subscription = Subscription::fromJsonLd($this->services->body->json($request));
        $this->validateTopic($subscription->topicType, $subscription->topic, $agent);
        $created = (new ActionRequests($this->services))->create($subscription, $agent->iri);

        return $this->services->responder->empty(201, $negotiated, ['Location' => $created->iri->value, 'Type' => Api::SubscriptionRequest]);
    }

    /**
     * A type topic must be a logistics object class; an identifier topic must
     * be an object this server holds (hidden ones look non-existent).
     */
    private function validateTopic(TopicType $topicType, string $topic, ?Agent $agent = null): void
    {
        if ($topicType === TopicType::Type) {
            if (!$this->services->vocabulary->isLogisticsObjectClass($topic)) {
                throw HttpException::badRequest(\sprintf('"%s" is not a logistics object type of the ONE Record ontology.', $topic), Api::hasTopic, 'Invalid resource');
            }

            return;
        }
        try {
            $iri = new Iri($topic);
        } catch (InvalidArgumentException) {
            throw HttpException::badRequest('topic must be a logistics object URI.', Api::hasTopic, 'Invalid resource');
        }
        if (!$this->services->config->isLocal($iri)) {
            throw HttpException::badRequest(\sprintf('"%s" is not a logistics object of this server.', $topic), Api::hasTopic, 'Invalid resource');
        }
        $hidden = $agent !== null && $this->services->policy->decide($agent, Action::ReadLogisticsObject, $iri) === Decision::Hide;
        if ($hidden || !$this->services->objects->exists($iri)) {
            throw HttpException::badRequest(\sprintf('"%s" is not a logistics object of this server.', $topic), Api::hasTopic, 'Invalid resource');
        }
    }

    /** @internal */
    public static function compactTopicType(TopicType $type): string
    {
        return Nodes::compact($type->value);
    }
}
