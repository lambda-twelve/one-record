<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use RuntimeException;

/**
 * Anything that stops the client from getting an answer it understands: a
 * transport failure, an unreadable body, no common API version, no token.
 * HTTP error answers are the subclass OneRecordHttpException.
 */
class ClientException extends RuntimeException {}
