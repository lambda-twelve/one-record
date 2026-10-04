<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Throwable;

/**
 * What a host's outbox worker does after a failed delivery. Every host needs
 * the same classification, so it lives here: transport failures and the
 * statuses that say "not now" (5xx, 408, 429) are retried with backoff;
 * every other HTTP answer is the recipient's final word; anything else is a
 * defect on the sending side and not worth a retry either.
 */
enum DeliveryVerdict
{
    case Retry;
    case Reject;

    public static function of(Throwable $failure): self
    {
        if ($failure instanceof OneRecordHttpException) {
            return $failure->status >= 500 || $failure->status === 408 || $failure->status === 429 ? self::Retry : self::Reject;
        }
        if ($failure instanceof ClientExceptionInterface) {
            return self::Retry;
        }

        return self::Reject;
    }
}
