<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * A notification the server wants delivered to a partner. The host sends it
 * (POST {partner}/notifications with its own client and egress controls),
 * retries it, and keeps it until the partner confirms, as the spec's
 * guaranteed-delivery rule asks.
 */
final readonly class OutboundNotification
{
    public function __construct(
        public Iri $recipient,
        public Notification $notification,
        public DateTimeImmutable $createdAt,
    ) {}

    /**
     * The partner's notifications endpoint derived from its Organization URI
     * ({base}/logistics-objects/{id} → {base}/notifications), which is what the
     * spec means by "the callback URL can be derived from the subscriber". A
     * host may know better and ignore this.
     */
    public function suggestedEndpoint(): ?string
    {
        $position = strpos($this->recipient->value, '/logistics-objects/');

        return $position === false ? null : substr($this->recipient->value, 0, $position) . '/notifications';
    }
}
