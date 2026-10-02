<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Api\InvalidDocument;

/**
 * A Change document that cannot be understood (400 Bad Request at the API).
 */
final class ChangeException extends InvalidDocument {}
