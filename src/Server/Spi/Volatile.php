<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

/**
 * Marks a store that keeps nothing beyond the process, so it has nothing a
 * unit of work could roll back. The SDK's in-memory stores carry it; a
 * decorator around one should carry it too. Services warns about an identity
 * UnitOfWork only in front of stores without it.
 */
interface Volatile {}
