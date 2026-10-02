<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * api:Severity (API 2.3.0). An error without a severity is an ERROR.
 */
enum Severity: string
{
    case Error = Namespaces::API . 'ERROR';
    case Warning = Namespaces::API . 'WARNING';
}
