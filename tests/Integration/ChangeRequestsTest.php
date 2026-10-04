<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\Event\ActionRequestCreated;
use LambdaTwelve\OneRecord\Server\Event\ActionRequestStatusChanged;
use LambdaTwelve\OneRecord\Server\Event\LogisticsObjectRevised;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use Psr\Http\Message\ResponseInterface;

/**
 * PATCH /logistics-objects/{id} and the ChangeRequest lifecycle through
 * /action-requests/{id} and the holder's PHP API.
 */
final class ChangeRequestsTest extends ServerTestCase
{
    private function change(LogisticsObject $from, LogisticsObject $to, int $revision = 1): Change
    {
        $change = (new ChangeBuilder($this->server->services->vocabulary))->diff($from, $to, $revision, 'Test change');
        self::assertNotNull($change);

        return $change;
    }

    private function heavier(string $id = 'piece-1', float $weight = 25.0, string $description = 'Books'): LogisticsObject
    {
        return ObjectBuilder::of(Cargo::Piece)
            ->set(Cargo::goodsDescription, $description)
            ->set(Cargo::coload, false)
            ->set(Cargo::grossWeight, Values::quantity($weight, MeasurementUnitCode::KGM))
            ->build(new Iri(self::BASE . '/logistics-objects/' . $id));
    }

    /**
     * @param array<string, string> $headers
     */
    private function patch(string $body, string $agent = self::PARTNER, string $id = 'piece-1', array $headers = []): ResponseInterface
    {
        return $this->request('PATCH', '/logistics-objects/' . $id, $agent, $headers, $body);
    }

    /**
     * @return array<string, mixed>
     */
    private function actionRequest(string $location, string $agent = self::PARTNER, string $version = '2.3.0'): array
    {
        $response = $this->request('GET', substr($location, \strlen(self::BASE)), $agent, ['Accept' => 'application/ld+json; version=' . $version]);
        self::assertSame(200, $response->getStatusCode(), (string) $response->getBody());

        return self::json($response);
    }

    public function testAPartnerChangeBecomesAPendingRequestTheHolderAccepts(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $change = $this->change($piece, $this->heavier());

        $response = $this->patch($change->toJson());

        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame('', (string) $response->getBody());
        self::assertSame(Api::ChangeRequest, $response->getHeaderLine('Type'));
        $location = $response->getHeaderLine('Location');
        self::assertStringStartsWith(self::BASE . '/action-requests/', $location);
        self::assertCount(1, $this->dispatcher->of(ActionRequestCreated::class));

        // Nothing applied yet: the object is still at revision 1.
        $read = $this->request('GET', '/logistics-objects/piece-1');
        self::assertSame('1', $read->getHeaderLine('Latest-Revision'));

        $body = $this->actionRequest($location);
        self::assertSame('api:ChangeRequest', $body['@type']);
        self::assertSame($location, $body['@id']);
        self::assertSame(['@id' => 'api:REQUEST_PENDING'], $body['api:hasRequestStatus']);
        self::assertSame(['@id' => self::PARTNER], $body['api:isRequestedBy']);
        self::assertArrayHasKey('api:hasRequestStatusSince', $body, '2.3.0 carries the status timestamp');
        $embedded = self::arr($body['api:hasChange']);
        self::assertSame('api:Change', $embedded['@type']);
        self::assertSame('Test change', $embedded['api:hasDescription']);

        $at22 = $this->actionRequest($location, version: '2.2.0');
        self::assertArrayNotHasKey('api:hasRequestStatusSince', $at22, '2.2.0 does not know the property');
        self::assertArrayNotHasKey('api:hasRequestStatusHistory', $at22);

        // The holder decides in PHP.
        $this->clock->advance('+5 minutes');
        $accepted = (new DataHolder($this->server->services))->accept(new Iri($location));
        self::assertSame(RequestStatus::Accepted, $accepted->status);

        $read = $this->request('GET', '/logistics-objects/piece-1');
        self::assertSame('2', $read->getHeaderLine('Latest-Revision'));
        self::assertSame('Fri, 02 Oct 2026 12:05:00 GMT', $read->getHeaderLine('Last-Modified'));
        $json = self::json($read);
        self::assertSame(2, $json['api:hasRevision']);
        self::assertSame(['@type' => 'xsd:double', '@value' => '2.5E1'], self::arr($json['cargo:grossWeight'])['cargo:numericalValue'], 'an integral double is written with its datatype (R7-002)');

        $revised = $this->dispatcher->of(LogisticsObjectRevised::class);
        self::assertCount(1, $revised);
        self::assertSame([Cargo::grossWeight], $revised[0]->changedProperties);
        self::assertSame($location, $revised[0]->changeRequest->value);

        $body = $this->actionRequest($location);
        self::assertSame(['@id' => 'api:REQUEST_ACCEPTED'], $body['api:hasRequestStatus']);
        $history = self::arr($body['api:hasRequestStatusHistory']);
        self::assertCount(1, $history, 'the pending phase is history');
        self::assertSame(['@id' => 'api:REQUEST_PENDING'], self::arr($history[0])['api:hasRequestStatus']);

        // The audit trail shows the request as the object's history.
        $trail = self::json($this->request('GET', '/logistics-objects/piece-1/audit-trail'));
        self::assertSame('api:AuditTrail', $trail['@type']);
        self::assertSame(['@type' => 'http://www.w3.org/2001/XMLSchema#positiveInteger', '@value' => '2'], $trail['api:hasLatestRevision'], 'typed, as in the spec example');
        $requests = self::arr($trail['api:hasActionRequest']);
        self::assertCount(1, $requests);
        self::assertSame($location, self::arr($requests[0])['@id']);
    }

    public function testTheHoldersOwnPatchIsAppliedAtOnce(): void
    {
        $piece = $this->storePiece();
        $response = $this->patch($this->change($piece, $this->heavier())->toJson(), self::HOLDER);

        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        $read = $this->request('GET', '/logistics-objects/piece-1');
        self::assertSame('2', $read->getHeaderLine('Latest-Revision'));
        $body = $this->actionRequest($response->getHeaderLine('Location'), self::HOLDER);
        self::assertSame(['@id' => 'api:REQUEST_ACCEPTED'], $body['api:hasRequestStatus']);
    }

    public function testTheHolderDecidesOverHttpToo(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $location = $this->patch($this->change($piece, $this->heavier())->toJson())->getHeaderLine('Location');
        $path = substr($location, \strlen(self::BASE));

        // Deciding is internal: a partner, even the requestor, may not.
        self::assertError($this->request('PATCH', $path . '?status=REQUEST_ACCEPTED', self::PARTNER), 403);
        self::assertError($this->request('PATCH', $path . '?status=REQUEST_ACCEPTED', self::STRANGER), 403);

        self::assertError($this->request('PATCH', $path, self::HOLDER), 400, 'Invalid query parameter');
        self::assertError($this->request('PATCH', $path . '?status=REQUEST_FAILED', self::HOLDER), 400, 'Invalid query parameter');
        self::assertError($this->request('PATCH', '/action-requests/nope?status=REQUEST_ACCEPTED', self::HOLDER), 404);

        $decided = $this->request('PATCH', $path . '?status=REQUEST_ACCEPTED', self::HOLDER);
        self::assertSame(204, $decided->getStatusCode(), (string) $decided->getBody());
        self::assertSame('', (string) $decided->getBody());
        self::assertSame(Api::ChangeRequest, $decided->getHeaderLine('Type'));
        self::assertSame('2', $this->request('GET', '/logistics-objects/piece-1')->getHeaderLine('Latest-Revision'));

        // Accepting twice is not a transition the state machine has: 422 at 2.3, 400 at 2.2.
        self::assertError($this->request('PATCH', $path . '?status=REQUEST_ACCEPTED', self::HOLDER), 422);
        self::assertError($this->request('PATCH', $path . '?status=REQUEST_REJECTED', self::HOLDER, ['Accept' => 'application/ld+json; version=2.2.0']), 400, 'Invalid resource');

        // The full IRI spelling of the status works as well.
        $piece2 = $this->storePiece('piece-2');
        $this->server->policy->allow(new Iri(self::PARTNER), $piece2->iri, [Permission::PatchLogisticsObject]);
        $location2 = $this->patch($this->change($piece2, $this->heavier('piece-2'))->toJson(), id: 'piece-2')->getHeaderLine('Location');
        $rejected = $this->request('PATCH', substr($location2, \strlen(self::BASE)) . '?status=' . rawurlencode(Api::REQUEST_REJECTED), self::HOLDER);
        self::assertSame(204, $rejected->getStatusCode());
        self::assertSame(['@id' => 'api:REQUEST_REJECTED'], $this->actionRequest($location2)['api:hasRequestStatus']);
        self::assertSame('1', $this->request('GET', '/logistics-objects/piece-2')->getHeaderLine('Latest-Revision'));
    }

    public function testRejectionCarriesErrorsAndNotifiesARequesterWhoAsked(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $change = $this->change($piece, $this->heavier());
        $json = $change->toJsonLd();
        $json['api:notifyRequestStatusChange'] = true;
        $location = $this->patch(json_encode($json, JSON_THROW_ON_ERROR))->getHeaderLine('Location');

        $holder = new DataHolder($this->server->services);
        $holder->reject(new Iri($location), [Error::of('Weight not plausible', '422', 'Pieces of books do not weigh 25 kg.', Cargo::grossWeight)]);

        $body = $this->actionRequest($location);
        self::assertSame(['@id' => 'api:REQUEST_REJECTED'], $body['api:hasRequestStatus']);
        $errors = self::arr($body['api:hasError']);
        self::assertCount(1, $errors);
        self::assertSame('Weight not plausible', self::arr($errors[0])['api:hasTitle']);

        $notifications = $this->server->outbox->all();
        self::assertCount(2, $notifications, 'one when the request was created (pending), one on rejection');
        self::assertSame(self::PARTNER, $notifications[1]->recipient->value);
        self::assertSame(NotificationEventType::ChangeRequestRejected, $notifications[1]->notification->eventType);
        self::assertSame($location, $notifications[1]->notification->triggeredBy?->value);
        self::assertSame($piece->iri->value, $notifications[1]->notification->logisticsObject?->value);
        self::assertSame(Cargo::Piece, $notifications[1]->notification->logisticsObjectType);

        $changed = $this->dispatcher->of(ActionRequestStatusChanged::class);
        self::assertCount(1, $changed);
        self::assertSame(RequestStatus::Pending, $changed[0]->previous);
        self::assertSame(RequestStatus::Rejected, $changed[0]->request->status);
    }

    public function testAcceptingAStaleChangeRejectsTheOtherPendingOnes(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $this->server->policy->allow(new Iri(self::STRANGER), $piece->iri, [Permission::PatchLogisticsObject, Permission::GetLogisticsObject]);

        $first = $this->patch($this->change($piece, $this->heavier())->toJson())->getHeaderLine('Location');
        $second = $this->patch($this->change($piece, $this->heavier(weight: 30.0))->toJson(), self::STRANGER)->getHeaderLine('Location');

        $holder = new DataHolder($this->server->services);
        $holder->accept(new Iri($first));

        self::assertSame(['@id' => 'api:REQUEST_ACCEPTED'], $this->actionRequest($first)['api:hasRequestStatus']);
        $other = $this->actionRequest($second, self::STRANGER);
        self::assertSame(['@id' => 'api:REQUEST_REJECTED'], $other['api:hasRequestStatus']);
        self::assertStringContainsString('revision', self::str(self::arr(self::arr($other['api:hasError'])[0])['api:hasTitle']));

        // A change written against revision 1 is still accepted as a request, and fails when applied.
        $late = $this->patch($this->change($piece, $this->heavier(weight: 31.0), 1)->toJson())->getHeaderLine('Location');
        $failed = $holder->accept(new Iri($late));
        self::assertSame(RequestStatus::Failed, $failed->status);
        self::assertSame('409', $failed->errors[0]->details[0]->code);
        self::assertSame('2', $this->request('GET', '/logistics-objects/piece-1')->getHeaderLine('Latest-Revision'));
    }

    public function testAChangeThatCannotBeAppliedFails(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);

        // Deleting a value the object does not have is detected on acceptance, not on receipt.
        $json = $this->change($piece, $this->heavier())->toJsonLd();
        $operations = self::arr($json['api:hasOperation']);
        foreach ($operations as $i => $operation) {
            $operation = self::arr($operation);
            if (self::arr($operation['api:op'])['@id'] === 'api:DELETE') {
                $objects = self::arr($operation['api:o']);
                $objects[0] = [...self::arr($objects[0]), 'api:hasValue' => '999'];
                $operation['api:o'] = $objects;
                $operations[$i] = $operation;
            }
        }
        $json['api:hasOperation'] = $operations;
        $location = $this->patch(json_encode($json, JSON_THROW_ON_ERROR))->getHeaderLine('Location');

        $result = (new DataHolder($this->server->services))->accept(new Iri($location));

        self::assertSame(RequestStatus::Failed, $result->status);
        $body = $this->actionRequest($location);
        self::assertSame(['@id' => 'api:REQUEST_FAILED'], $body['api:hasRequestStatus']);
        self::assertNotEmpty($body['api:hasError']);
        self::assertSame('1', $this->request('GET', '/logistics-objects/piece-1')->getHeaderLine('Latest-Revision'));
    }

    public function testRequestorsRevokeWithDelete(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $location = $this->patch($this->change($piece, $this->heavier())->toJson())->getHeaderLine('Location');
        $path = substr($location, \strlen(self::BASE));

        self::assertError($this->request('DELETE', $path, self::STRANGER), 403, 'Not authorized');
        self::assertError($this->request('DELETE', '/action-requests/nope', self::PARTNER), 404);

        $revoked = $this->request('DELETE', $path, self::PARTNER);
        self::assertSame(204, $revoked->getStatusCode(), (string) $revoked->getBody());
        self::assertSame(Api::ChangeRequest, $revoked->getHeaderLine('Type'));
        $body = $this->actionRequest($location);
        self::assertSame(['@id' => 'api:REQUEST_REVOKED'], $body['api:hasRequestStatus']);
        self::assertSame(['@id' => self::PARTNER], $body['api:isRevokedBy']);

        self::assertError($this->request('DELETE', $path, self::PARTNER), 422);
        self::assertError($this->request('DELETE', $path, self::PARTNER, ['Accept' => 'application/ld+json; version=2.2.0']), 400);

        // An accepted change cannot be revoked either: the revision exists.
        $piece2 = $this->storePiece('piece-2');
        $this->server->policy->allow(new Iri(self::PARTNER), $piece2->iri, [Permission::PatchLogisticsObject]);
        $location2 = $this->patch($this->change($piece2, $this->heavier('piece-2'))->toJson(), id: 'piece-2')->getHeaderLine('Location');
        (new DataHolder($this->server->services))->accept(new Iri($location2));
        self::assertError($this->request('DELETE', substr($location2, \strlen(self::BASE)), self::PARTNER), 422);
    }

    public function testActionRequestsAreVisibleToTheirPartiesOnly(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $location = $this->patch($this->change($piece, $this->heavier())->toJson())->getHeaderLine('Location');
        $path = substr($location, \strlen(self::BASE));

        self::assertSame(200, $this->request('GET', $path, self::PARTNER)->getStatusCode());
        self::assertSame(200, $this->request('GET', $path, self::HOLDER)->getStatusCode());
        self::assertError($this->request('GET', $path, self::STRANGER), 404, 'Resource not found');
        self::assertError($this->request('GET', '/action-requests/nope', self::PARTNER), 404);
        self::assertError($this->request('GET', $path, null), 401);

        $head = $this->request('HEAD', $path, self::PARTNER);
        self::assertSame(200, $head->getStatusCode());
        self::assertSame('', (string) $head->getBody());
        self::assertSame(Api::ChangeRequest, $head->getHeaderLine('Type'));
        self::assertSame('Fri, 02 Oct 2026 12:00:00 GMT', $head->getHeaderLine('Last-Modified'));
    }

    public function testPatchIsValidated(): void
    {
        $piece = $this->storePiece();
        $this->storePiece('piece-2');
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $good = $this->change($piece, $this->heavier());

        self::assertError($this->patch($good->toJson(), self::STRANGER), 403);
        self::assertError($this->patch($good->toJson(), id: 'nope'), 404);
        self::assertError($this->patch($good->toJson(), id: 'piece-2'), 403, 'Not authorized');

        $wrongTarget = $this->change($this->piece('piece-2'), $this->heavier('piece-2'));
        self::assertError($this->patch($wrongTarget->toJson()), 400, 'Invalid resource');

        $ahead = $this->change($piece, $this->heavier(), 7);
        self::assertError($this->patch($ahead->toJson()), 422);

        self::assertError($this->patch('{"@context": {"api": "https://onerecord.iata.org/ns/api#"}, "@type": "api:Change"}'), 400, 'Invalid');
        self::assertError($this->patch('nope'), 400, 'Invalid body request');
        self::assertError($this->patch($good->toJson(), headers: ['Content-Type' => 'text/turtle']), 415);

        $hidden = $this->makeServer(Decision::Hide);
        $this->server = $hidden;
        $this->storePiece('piece-3', null);
        self::assertError($this->patch($good->toJson(), id: 'piece-3'), 404);
    }
}
