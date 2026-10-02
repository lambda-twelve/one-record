<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary;

use LambdaTwelve\OneRecord\Spec\Edition;

interface OntologySource
{
    /**
     * The Turtle text of one ontology file at one edition.
     */
    public function fetch(Edition $edition, OntologyFile $file): string;
}
