<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Throwable;

/**
 * What a host's outbox worker does after a failed delivery. Every host needs
 * the same classification, so it lives here: transport failures and the
 * statuses that say "not now" (5xx, 408, 429) are retried with backoff,
 * whether they came from the notification itself or from fetching the token
 * for it; every other HTTP answer, and refused credentials, is final; anything
 * else is a defect on the sending side and not worth a retry either.
 *
 * Pass whatever you caught. The SDK client wraps transport failures in a
 * ClientException with the PSR exception as its cause, so the chain of
 * previous exceptions is walked and the first cause that says something
 * decides.
 */
enum DeliveryVerdict
{
    case Retry;
    case Reject;

    public static function of(Throwable $failure): self
    {
        for ($cause = $failure; $cause !== null; $cause = $cause->getPrevious()) {
            if ($cause instanceof OneRecordHttpException) {
                return self::forStatus($cause->status);
            }
            if ($cause instanceof TokenEndpointException) {
                return self::forStatus($cause->status);
            }
            if ($cause instanceof ClientExceptionInterface) {
                return self::Retry;
            }
        }

        return self::Reject;
    }

    private static function forStatus(int $status): self
    {
        return $status >= 500 || $status === 408 || $status === 429 ? self::Retry : self::Reject;
    }
}
