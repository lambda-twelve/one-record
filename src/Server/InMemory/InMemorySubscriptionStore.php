<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\SubscriptionStore;

/**
 * Publisher side derived from the action requests (an accepted
 * SubscriptionRequest is a subscription); subscriber side from a list the
 * host fills with the subscriptions it wants to offer.
 */
final class InMemorySubscriptionStore implements SubscriptionStore
{
    /** @var list<Subscription> */
    private array $offered = [];

    public function __construct(private readonly ActionRequestStore $requests) {}

    public function offer(Subscription $subscription): void
    {
        $this->offered[] = $subscription;
    }

    public function subscribersOf(Iri $logisticsObject, array $types, DateTimeImmutable $now): array
    {
        $out = [];
        foreach ($this->requests instanceof InMemoryActionRequestStore ? $this->requests->all() : [] as $request) {
            if ($request->type !== ActionRequestType::Subscription || $request->status !== RequestStatus::Accepted) {
                continue;
            }
            $subscription = $request->payload;
            if (!$subscription instanceof Subscription || $subscription->isExpiredAt($now) || !$subscription->covers($logisticsObject, $types)) {
                continue;
            }
            $out[] = ['subscription' => $subscription, 'request' => $request->iri];
        }

        return $out;
    }

    public function offered(TopicType $topicType, string $topic): array
    {
        return array_values(array_filter($this->offered, static fn(Subscription $s): bool => $s->topicType === $topicType && $s->topic === $topic));
    }

    /** @internal for tests */
    public static function isAccepted(ActionRequest $request): bool
    {
        return $request->status === RequestStatus::Accepted;
    }
}
