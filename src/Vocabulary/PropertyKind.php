<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary;

/**
 * Whether a property's values are nodes (IRIs, embedded objects) or literals.
 */
enum PropertyKind: string
{
    case Object = 'object';
    case Datatype = 'datatype';
}
