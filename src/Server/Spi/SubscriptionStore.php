<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Both sides of publish/subscribe. As publisher: the accepted subscription
 * requests that make partners subscribers of this server's objects. As
 * subscriber: the subscriptions this host wants to offer when a partner asks
 * GET /subscriptions?topicType&topic.
 */
interface SubscriptionStore
{
    /**
     * Accepted, unexpired subscriptions covering an object, with the action
     * request each came from (notifications reference it in isTriggeredBy).
     *
     * @param list<string> $types class IRIs of the object
     * @return list<array{subscription: Subscription, request: Iri}>
     */
    public function subscribersOf(Iri $logisticsObject, array $types, DateTimeImmutable $now): array;

    /**
     * What this host answers when asked for its subscription information.
     *
     * @return list<Subscription>
     */
    public function offered(TopicType $topicType, string $topic): array;
}
