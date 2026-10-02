<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * api:PatchOperation: ONE Record limits JSON-LD PATCH to ADD and DELETE;
 * replace is a DELETE followed by an ADD.
 */
enum OperationKind: string
{
    case Add = Namespaces::API . 'ADD';
    case Delete = Namespaces::API . 'DELETE';
}
