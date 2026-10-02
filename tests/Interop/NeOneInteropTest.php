<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Interop;

use GuzzleHttp\Client as Guzzle;
use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Signer;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Client\OneRecordClient;
use LambdaTwelve\OneRecord\Client\OneRecordHttpException;
use LambdaTwelve\OneRecord\Client\StaticTokenProvider;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\Model\Builder\Embedded;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LocalGraph;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\UuidIriMinter;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\InMemory\InMemoryServer;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Server\SystemClock;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Tests\Support\HeaderAuthenticator;
use LambdaTwelve\OneRecord\Tests\Support\InProcessHttpClient;
use LambdaTwelve\OneRecord\Tests\Support\RecordingDispatcher;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * NE:ONE as the comparison oracle: the same graph is published to a running
 * NE:ONE (through this package's client) and to this package's server, both
 * are read back and compared as RDF. Opt in with ONE_RECORD_NEONE_URL (see
 * tests/Interop/run.sh); every difference is either a bug here or an entry
 * in docs/spec-questions.md, never silently normalised away without a note.
 */
#[Group('interop')]
final class NeOneInteropTest extends TestCase
{
    private const string OURS = 'https://1r.example.com';
    private const string PARTNER_AGENT = 'https://1r.forwarder.example/logistics-objects/forwarder';

    private string $neone;
    private Rs256Signer $signer;
    private Psr17Factory $factory;
    private Guzzle $http;
    private InMemoryServer $ours;
    private DataHolder $ourHolder;
    private string $run;

    protected function setUp(): void
    {
        $url = getenv('ONE_RECORD_NEONE_URL');
        if (!\is_string($url) || $url === '') {
            self::markTestSkipped('Set ONE_RECORD_NEONE_URL to a running NE:ONE (tests/Interop/run.sh does).');
        }
        $this->neone = rtrim($url, '/');
        $key = getenv('ONE_RECORD_NEONE_PRIVATE_KEY');
        $issuer = getenv('ONE_RECORD_NEONE_ISSUER');
        if (!\is_string($key) || !is_file($key) || !\is_string($issuer)) {
            self::fail('ONE_RECORD_NEONE_PRIVATE_KEY must point at the PEM key NE:ONE trusts and ONE_RECORD_NEONE_ISSUER name its issuer.');
        }
        $this->signer = new Rs256Signer((string) file_get_contents($key), $issuer, new SystemClock());
        $this->factory = new Psr17Factory();
        $this->http = new Guzzle(['http_errors' => false, 'timeout' => 15]);
        $this->run = bin2hex(random_bytes(4));

        $holder = new Iri(self::OURS . '/logistics-objects/holder');
        $this->ours = new InMemoryServer(new ServerConfig(self::OURS, $holder, dataModelVersions: null, dataHolderType: Cargo::Company), new HeaderAuthenticator(), new SystemClock(), new RecordingDispatcher(), $this->factory, $this->factory);
        $this->ours->policy->addInternal($holder);
        $this->ourHolder = new DataHolder($this->ours->services);
    }

    private function neoneHolderAgent(): string
    {
        return $this->neone . '/logistics-objects/_data-holder';
    }

    private function neoneClient(string $agent): OneRecordClient
    {
        $token = $this->signer->sign(['sub' => $agent, 'logistics_agent_uri' => $agent], 600);

        return new OneRecordClient($this->http, $this->factory, $this->factory, new StaticTokenProvider($token), $this->neone);
    }

    private function ourClient(string $agent): OneRecordClient
    {
        return new OneRecordClient(new InProcessHttpClient($this->ours->handler), $this->factory, $this->factory, new StaticTokenProvider($agent), self::OURS);
    }

    /**
     * A shipment with a piece and a company, linked both ways, with embedded
     * values and a party: every shape a forwarder's mapping produces.
     */
    private function graph(float $weight = 190.5): LocalGraph
    {
        return LocalGraph::create()
            ->add('shipment', ObjectBuilder::of(Cargo::Shipment)
                ->set(Cargo::goodsDescription, 'Machine parts ' . $this->run)
                ->set(Cargo::totalGrossWeight, Values::quantity($weight, MeasurementUnitCode::KGM))
                ->add(Cargo::pieces, Values::ref('piece'))
                ->add(Cargo::involvedParties, Embedded::of(Cargo::Party)
                    ->set(Cargo::partyRole, Values::code('ParticipantIdentifier', 'SHP'))
                    ->set(Cargo::partyDetails, Values::ref('shipper'))))
            ->add('piece', ObjectBuilder::of(Cargo::Piece)
                ->set(Cargo::goodsDescription, 'Machine parts ' . $this->run)
                ->set(Cargo::grossWeight, Values::quantity($weight, MeasurementUnitCode::KGM))
                ->set(Cargo::coload, false)
                ->add(Cargo::specialHandlingCodes, Values::code('SpecialHandlingCode', 'VAL'))
                ->set(Cargo::ofShipment, Values::ref('shipment')))
            ->add('shipper', ObjectBuilder::of(Cargo::Company)
                ->set(Cargo::name, 'Shipper ' . $this->run))
            ->root('shipment');
    }

    /**
     * Publishes the graph to both servers.
     *
     * @return array{neone: array<string, Iri>, ours: array<string, Iri>} IRIs by local key
     */
    private function publishBoth(LocalGraph $graph): array
    {
        $client = $this->neoneClient($this->neoneHolderAgent());
        $resolved = $graph->resolve(new UuidIriMinter($this->neone, seed: 'interop-' . $this->run));
        foreach ($resolved->objects as $object) {
            // NE:ONE honours a predefined @id under its own base, as this server does.
            $created = $client->createLogisticsObject($object);
            self::assertSame($object->iri->value, $created->value, 'NE:ONE keeps the predefined URI');
        }
        $ours = $this->ourHolder->publish($graph, new UuidIriMinter(self::OURS, seed: 'interop-' . $this->run));

        return ['neone' => $resolved->iris(), 'ours' => $ours->iris()];
    }

    /**
     * Compares two logistics objects modulo the things that legitimately
     * differ between servers: the URIs (rewritten to local keys), embedded-node
     * ids (treated as blank nodes) and inferred superclass types (spec
     * question 21: NE:ONE adds every superclass to @type).
     *
     * @param array<string, Iri> $neoneIris
     * @param array<string, Iri> $ourIris
     */
    private function assertSameObject(LogisticsObject $neone, LogisticsObject $ours, array $neoneIris, array $ourIris, string $what): void
    {
        $left = self::normalise($neone->graph, $neoneIris);
        $right = self::normalise($ours->graph, $ourIris);
        $diff = (new Comparer(['internal:', 'neone:']))->compare($left, $right);
        self::assertTrue($diff->isEqual(), $what . ": NE:ONE and this server disagree.\n" . $diff->describe());
    }

    /**
     * @param array<string, Iri> $iris local key => IRI on that server
     */
    private static function normalise(Graph $graph, array $iris): Graph
    {
        $vocabulary = Vocabulary::default();
        $byIri = [];
        foreach ($iris as $key => $iri) {
            $byIri[$iri->value] = new Iri('local:' . $key);
        }
        $rename = static fn(Iri|BlankNode $term): Iri|BlankNode => $term instanceof Iri && isset($byIri[$term->value]) ? $byIri[$term->value] : $term;
        $out = new Graph();
        $types = [];
        foreach ($graph as $triple) {
            $subject = $rename($triple->subject);
            $object = $triple->object instanceof Iri || $triple->object instanceof BlankNode ? $rename($triple->object) : $triple->object;
            if ($triple->predicate->value === Graph::RDF_TYPE && $object instanceof Iri) {
                $types[$subject->toNTriples()][] = [$subject, $object];
                continue;
            }
            $out->add(new Triple($subject, $triple->predicate, $object));
        }
        foreach ($types as $entries) {
            $iris = array_map(static fn(array $e): string => $e[1]->value, $entries);
            $keep = $vocabulary->mostSpecific($iris);
            foreach ($entries as [$subject, $type]) {
                if (\in_array($type->value, $keep, true)) {
                    $out->add(new Triple($subject, new Iri(Graph::RDF_TYPE), $type));
                }
            }
        }

        return $out;
    }

    /**
     * NE:ONE evaluates action requests on a timer; wait for a decision.
     */
    private function awaitDecision(OneRecordClient $client, Iri $request, int $seconds = 30): RequestStatus
    {
        $deadline = microtime(true) + $seconds;
        do {
            $status = $client->getActionRequest($request)->status;
            if ($status !== RequestStatus::Pending) {
                return $status;
            }
            usleep(500_000);
        } while (microtime(true) < $deadline);
        self::fail(\sprintf('%s is still pending after %d seconds.', $request->value, $seconds));
    }

    public function testServerInformationIsDiscoveredAndTheVersionNegotiated(): void
    {
        $client = $this->neoneClient($this->neoneHolderAgent());
        $information = $client->serverInformation();

        self::assertSame($this->neoneHolderAgent(), $information->dataHolder->value);
        self::assertContains('2.2.0', $information->apiVersions);
        self::assertSame(ApiVersion::V2_2_0, $client->apiVersion(), 'NE:ONE speaks 2.2.0, so we do');
        self::assertContains('https://onerecord.iata.org/ns/cargo#', $information->ontologies);
    }

    public function testTheSameGraphReadsBackIdenticallyFromBothServers(): void
    {
        $iris = $this->publishBoth($this->graph());
        $neone = $this->neoneClient($this->neoneHolderAgent());
        $ours = $this->ourClient(self::OURS . '/logistics-objects/holder');

        foreach (['shipment', 'piece', 'shipper'] as $key) {
            $left = $neone->getLogisticsObject($iris['neone'][$key]);
            $right = $ours->getLogisticsObject($iris['ours'][$key]);
            self::assertSame(1, $left->revision);
            self::assertSame(1, $right->revision);
            self::assertNotNull($left->object);
            self::assertNotNull($right->object);
            $this->assertSameObject($left->object, $right->object, $iris['neone'], $iris['ours'], $key);
        }
    }

    public function testAChangeIsAppliedTheSameWay(): void
    {
        $graph = $this->graph();
        $iris = $this->publishBoth($graph);
        $neone = $this->neoneClient($this->neoneHolderAgent());
        $ours = $this->ourClient(self::OURS . '/logistics-objects/holder');

        $before = $neone->getLogisticsObject($iris['neone']['piece'])->object;
        self::assertNotNull($before);
        $after = ObjectBuilder::of(Cargo::Piece)
            ->set(Cargo::goodsDescription, 'Machine parts, repacked ' . $this->run)
            ->set(Cargo::grossWeight, Values::quantity(192.0, MeasurementUnitCode::KGM))
            ->set(Cargo::coload, false)
            ->add(Cargo::specialHandlingCodes, Values::code('SpecialHandlingCode', 'VAL'))
            ->set(Cargo::ofShipment, $iris['neone']['shipment'])
            ->build($iris['neone']['piece']);
        $change = (new ChangeBuilder())->diff($before, $after, 1, 'Repacked');
        self::assertInstanceOf(Change::class, $change);

        $request = $neone->requestChange($change);
        $status = $this->awaitDecision($neone, $request);
        self::assertSame(RequestStatus::Accepted, $status, 'the holder\'s own change is accepted on the next evaluation');

        // The same change on our side, with our URIs.
        $ourBefore = $ours->getLogisticsObject($iris['ours']['piece'])->object;
        self::assertNotNull($ourBefore);
        $ourAfter = $after->withIri($iris['ours']['piece'])->withGraph(self::rewrite($after->withIri($iris['ours']['piece'])->graph, [$iris['neone']['shipment']->value => $iris['ours']['shipment']]));
        $ourChange = (new ChangeBuilder())->diff($ourBefore, $ourAfter, 1, 'Repacked');
        self::assertInstanceOf(Change::class, $ourChange);
        $this->ourHolder->change($ourChange);

        $left = $neone->getLogisticsObject($iris['neone']['piece']);
        $right = $ours->getLogisticsObject($iris['ours']['piece']);
        self::assertSame(2, $left->latestRevision);
        self::assertSame(2, $right->latestRevision);
        self::assertNotNull($left->object);
        self::assertNotNull($right->object);
        $this->assertSameObject($left->object, $right->object, $iris['neone'], $iris['ours'], 'piece after change');

        $leftTrail = $neone->getAuditTrail($iris['neone']['piece']);
        $rightTrail = $ours->getAuditTrail($iris['ours']['piece']);
        self::assertSame(2, $leftTrail->latestRevision);
        self::assertSame(2, $rightTrail->latestRevision);
        self::assertCount(1, $leftTrail->requests);
        self::assertCount(1, $rightTrail->requests);
        self::assertSame(RequestStatus::Accepted, $leftTrail->requests[0]->status);
        self::assertSame(RequestStatus::Accepted, $rightTrail->requests[0]->status);
    }

    public function testEventsAreStoredAndListedTheSameWay(): void
    {
        $iris = $this->publishBoth($this->graph());
        $neone = $this->neoneClient($this->neoneHolderAgent());
        $ours = $this->ourClient(self::OURS . '/logistics-objects/holder');
        $event = [
            '@context' => ['cargo' => Cargo::NAMESPACE],
            '@type' => 'cargo:LogisticsEvent',
            'cargo:eventName' => 'Departed ' . $this->run,
            'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z'],
            'cargo:eventCode' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/StatusCode#DEP'],
            'cargo:eventTimeType' => ['@id' => 'cargo:ACTUAL'],
            'cargo:partialEventIndicator' => false,
        ];

        $leftEvent = $neone->postLogisticsEvent($iris['neone']['piece'], $event);
        $rightEvent = $ours->postLogisticsEvent($iris['ours']['piece'], $event);

        $left = $neone->getLogisticsEvents($iris['neone']['piece']);
        $right = $ours->getLogisticsEvents($iris['ours']['piece']);
        self::assertSame(1, $left->totalItems);
        self::assertSame(1, $right->totalItems);
        $leftGraph = self::withoutServerSideEventProperties(self::rewrite($left->events[0]->graph, [$leftEvent->value => new Iri('local:event'), $iris['neone']['piece']->value => new Iri('local:piece')]));
        $rightGraph = self::withoutServerSideEventProperties(self::rewrite($right->events[0]->graph, [$rightEvent->value => new Iri('local:event'), $iris['ours']['piece']->value => new Iri('local:piece')]));
        $diff = (new Comparer(['internal:', 'neone:']))->compare(self::normalise($leftGraph, []), self::normalise($rightGraph, []));
        self::assertTrue($diff->isEqual(), "Listed events differ.\n" . $diff->describe());

        self::assertSame(1, $neone->getLogisticsEvents($iris['neone']['piece'], new \LambdaTwelve\OneRecord\Client\EventFilter(eventCodes: ['DEP']))->totalItems);
        self::assertSame(0, $neone->getLogisticsEvents($iris['neone']['piece'], new \LambdaTwelve\OneRecord\Client\EventFilter(eventCodes: ['ARR']))->totalItems);
    }

    public function testAPartnerIsRefusedUntilAccessIsDelegated(): void
    {
        $iris = $this->publishBoth($this->graph());
        $neonePartner = $this->neoneClient(self::PARTNER_AGENT);
        $ourPartner = $this->ourClient(self::PARTNER_AGENT);
        $neoneHolder = $this->neoneClient($this->neoneHolderAgent());

        foreach ([$neonePartner, $ourPartner] as $i => $client) {
            try {
                $client->getLogisticsObject($iris[$i === 0 ? 'neone' : 'ours']['piece']);
                self::fail('a partner without a grant must be refused');
            } catch (OneRecordHttpException $e) {
                self::assertSame(403, $e->status);
            }
        }

        $delegation = $neonePartner->requestAccessDelegation(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::PARTNER_AGENT)], [$iris['neone']['piece']], 'Interop'));
        $neoneHolder->updateActionRequestStatus($delegation, RequestStatus::Accepted);
        // NE:ONE applies the decision on its evaluation tick, not within the PATCH (spec question 23).
        self::assertSame(RequestStatus::Accepted, $this->awaitDecision($neoneHolder, $delegation));
        self::assertSame(1, $neonePartner->getLogisticsObject($iris['neone']['piece'])->latestRevision, 'NE:ONE grants after acceptance');

        $ourDelegation = $ourPartner->requestAccessDelegation(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::PARTNER_AGENT)], [$iris['ours']['piece']], 'Interop'));
        $this->ourHolder->accept($ourDelegation);
        self::assertSame(1, $ourPartner->getLogisticsObject($iris['ours']['piece'])->latestRevision);
    }

    /**
     * @param array<string, Iri> $map IRI value => replacement
     */
    private static function rewrite(Graph $graph, array $map): Graph
    {
        $out = new Graph();
        foreach ($graph as $triple) {
            $s = $triple->subject instanceof Iri && isset($map[$triple->subject->value]) ? $map[$triple->subject->value] : $triple->subject;
            $o = $triple->object instanceof Iri && isset($map[$triple->object->value]) ? $map[$triple->object->value] : $triple->object;
            $out->add(new Triple($s, $triple->predicate, $o));
        }

        return $out;
    }

    /**
     * Properties a server sets on a received event (spec question 22):
     * NE:ONE records creationDate and the recording organisation itself.
     */
    private static function withoutServerSideEventProperties(Graph $graph): Graph
    {
        $out = new Graph();
        foreach ($graph as $triple) {
            if (\in_array($triple->predicate->value, [Cargo::creationDate, Cargo::recordingOrganization, Cargo::eventFor], true)) {
                continue;
            }
            $out->add($triple);
        }

        return $out;
    }
}
