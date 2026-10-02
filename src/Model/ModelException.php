<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use InvalidArgumentException;

/**
 * A document or graph that cannot be built: a property the class does not
 * accept, a value of the wrong kind, a reference to a key the graph lacks.
 */
final class ModelException extends InvalidArgumentException {}
