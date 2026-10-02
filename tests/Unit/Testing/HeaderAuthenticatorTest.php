<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Testing;

use LambdaTwelve\OneRecord\Testing\HeaderAuthenticator;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

final class HeaderAuthenticatorTest extends TestCase
{
    public function testTrustsTheHeaderInTheCliOnly(): void
    {
        $request = new ServerRequest('GET', 'https://1r.example.com/', [HeaderAuthenticator::HEADER => 'https://1r.partner.example/logistics-objects/partner']);

        self::assertSame('https://1r.partner.example/logistics-objects/partner', (new HeaderAuthenticator())->authenticate($request)?->iri->value);
        self::assertNull((new HeaderAuthenticator())->authenticate(new ServerRequest('GET', 'https://1r.example.com/')), 'no header, nobody');
        self::assertNull((new HeaderAuthenticator(sapi: 'fpm-fcgi'))->authenticate($request), 'under a web SAPI the bypass is off');
        self::assertNull((new HeaderAuthenticator(sapi: 'apache2handler'))->authenticate($request));
        self::assertNotNull((new HeaderAuthenticator(allowOutsideCli: true, sapi: 'fpm-fcgi'))->authenticate($request), 'only an explicit opt-in turns it on outside the CLI');
    }
}
