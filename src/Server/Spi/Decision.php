<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

/**
 * What a policy answers. Forbid is the spec's 403; Hide answers as if the
 * resource did not exist (404), for hosts that must not confirm existence to
 * parties without access.
 */
enum Decision
{
    case Allow;
    case Forbid;
    case Hide;

    public function allowed(): bool
    {
        return $this === self::Allow;
    }
}
