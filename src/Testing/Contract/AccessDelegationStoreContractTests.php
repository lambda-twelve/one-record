<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\AccessDelegationStore;
use LambdaTwelve\OneRecord\Server\Spi\Grant;

/**
 * The tests behind AccessDelegationStoreContract, as a trait, for hosts whose test cases must
 * extend a framework base class (Laravel's Testbench, Drupal's KernelTestBase)
 * and so cannot extend the abstract contract. Use it in any PHPUnit TestCase
 * and implement the abstract hook(s); the abstract class is this trait on a
 * bare TestCase.
 */
trait AccessDelegationStoreContractTests
{
    protected const string OBJECT = 'https://1r.example.com/logistics-objects/p1';
    protected const string OTHER = 'https://1r.example.com/logistics-objects/p2';
    protected const string PARTNER = 'https://1r.partner.example/logistics-objects/partner';
    protected const string STRANGER = 'https://1r.other.example/logistics-objects/other';
    protected const string REQUEST = 'https://1r.example.com/action-requests/d1';

    abstract protected function createStore(): AccessDelegationStore;

    public function testGrantsAreKeptPerAgentAndObjectWithTheirDetails(): void
    {
        $store = $this->createStore();
        self::assertSame([], $store->grantsFor(new Iri(self::PARTNER), new Iri(self::OBJECT)));

        $expires = new DateTimeImmutable('2026-12-31T23:59:59.000Z');
        $store->grant(new Grant(new Iri(self::PARTNER), new Iri(self::OBJECT), [Permission::GetLogisticsObject]));
        $store->grant(new Grant(new Iri(self::PARTNER), new Iri(self::OBJECT), [Permission::PatchLogisticsObject, Permission::PostLogisticsEvent], $expires, new Iri(self::REQUEST)));
        $store->grant(new Grant(new Iri(self::PARTNER), new Iri(self::OTHER), [Permission::GetLogisticsObject]));
        $store->grant(new Grant(new Iri(self::STRANGER), new Iri(self::OBJECT), [Permission::GetLogisticsEvent]));

        $grants = $store->grantsFor(new Iri(self::PARTNER), new Iri(self::OBJECT));
        self::assertCount(2, $grants);
        $sourced = array_values(array_filter($grants, static fn(Grant $g): bool => $g->source !== null));
        self::assertCount(1, $sourced);
        self::assertSame(self::REQUEST, $sourced[0]->source?->value);
        self::assertSame($expires->format(DATE_RFC3339_EXTENDED), $sourced[0]->expiresAt?->format(DATE_RFC3339_EXTENDED));
        self::assertTrue($sourced[0]->allows(Permission::PatchLogisticsObject));
        self::assertFalse($sourced[0]->allows(Permission::GetLogisticsObject));
        self::assertTrue($sourced[0]->isActiveAt(new DateTimeImmutable('2026-10-02T12:00:00Z')));
        self::assertFalse($sourced[0]->isActiveAt(new DateTimeImmutable('2027-01-01T00:00:00Z')));
        self::assertCount(1, $store->grantsFor(new Iri(self::PARTNER), new Iri(self::OTHER)));
        self::assertCount(1, $store->grantsFor(new Iri(self::STRANGER), new Iri(self::OBJECT)));
        self::assertSame([], $store->grantsFor(new Iri(self::STRANGER), new Iri(self::OTHER)));
    }

    public function testRevokeFromWithdrawsWhatOneRequestGranted(): void
    {
        $store = $this->createStore();
        $store->grant(new Grant(new Iri(self::PARTNER), new Iri(self::OBJECT), [Permission::GetLogisticsObject]));
        $store->grant(new Grant(new Iri(self::PARTNER), new Iri(self::OBJECT), [Permission::PatchLogisticsObject], null, new Iri(self::REQUEST)));
        $store->grant(new Grant(new Iri(self::STRANGER), new Iri(self::OTHER), [Permission::GetLogisticsObject], null, new Iri(self::REQUEST)));

        $store->revokeFrom(new Iri(self::REQUEST));

        $left = $store->grantsFor(new Iri(self::PARTNER), new Iri(self::OBJECT));
        self::assertCount(1, $left, 'the host\'s own grant stays');
        self::assertNull($left[0]->source);
        self::assertSame([], $store->grantsFor(new Iri(self::STRANGER), new Iri(self::OTHER)), 'every grant of the request, on any object');
        $store->revokeFrom(new Iri('https://1r.example.com/action-requests/unknown'));
    }

    public function testEraseForDropsEveryGrantOnOneObject(): void
    {
        $store = $this->createStore();
        $store->grant(new Grant(new Iri(self::PARTNER), new Iri(self::OBJECT), [Permission::GetLogisticsObject]));
        $store->grant(new Grant(new Iri(self::STRANGER), new Iri(self::OBJECT), [Permission::GetLogisticsObject], null, new Iri(self::REQUEST)));
        $store->grant(new Grant(new Iri(self::PARTNER), new Iri(self::OTHER), [Permission::GetLogisticsObject]));

        $store->eraseFor(new Iri(self::OBJECT));

        self::assertSame([], $store->grantsFor(new Iri(self::PARTNER), new Iri(self::OBJECT)));
        self::assertSame([], $store->grantsFor(new Iri(self::STRANGER), new Iri(self::OBJECT)));
        self::assertCount(1, $store->grantsFor(new Iri(self::PARTNER), new Iri(self::OTHER)));
    }
}
