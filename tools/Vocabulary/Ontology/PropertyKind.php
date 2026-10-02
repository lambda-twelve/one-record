<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology;

enum PropertyKind: string
{
    case Object = 'object';
    case Datatype = 'datatype';
}
