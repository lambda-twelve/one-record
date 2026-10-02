<?php

declare(strict_types=1);

/*
 * Generates the compliance collection (Postman v2.1 format, run with newman)
 * and prints it as JSON. The request set follows IATA's own Postman
 * collection; the bodies are the specification's published examples; the
 * assertions are the MUSTs of the API specification. The collection runs once
 * per supported API version: `apiVersion` drives the Accept header and the
 * version-dependent expectations.
 *
 *   php tests/Compliance/collection.php > collection.json
 *   newman run collection.json --env-var baseUrl=... --env-var apiVersion=2.3.0 \
 *       --env-var holderToken=... --env-var partnerToken=... --env-var partnerAgent=...
 *
 * tests/Compliance/run.sh does all of that against bin/serve.
 */

$fixtures = dirname(__DIR__) . '/Fixtures/spec/2026-07/';

// A fixture body with the example URIs replaced by collection variables.
$fixture = static function (string $name, array $replace = []) use ($fixtures): string {
    /** @var array<string, string> $replace */
    $body = file_get_contents($fixtures . $name);
    if ($body === false) {
        fwrite(STDERR, "Missing fixture $name\n");
        exit(1);
    }

    return strtr($body, $replace);
};

$helpers = <<<'JS'
    const v = pm.variables.get('apiVersion');
    const is23 = v === '2.3.0';
    const CARGO = 'https://onerecord.iata.org/ns/cargo#';
    const API = 'https://onerecord.iata.org/ns/api#';
    const header = (name) => pm.response.headers.get(name);
    function common(status, type) {
        pm.test('status is ' + status, () => pm.response.to.have.status(status));
        pm.test('Content-Type echoes the negotiated version', () => pm.expect(header('Content-Type')).to.eql('application/ld+json; version=' + v));
        pm.test('Content-Language is present', () => pm.expect(header('Content-Language')).to.be.a('string').that.is.not.empty);
        if (type) {
            pm.test('Type is ' + type, () => pm.expect(header('Type')).to.eql(type));
        }
    }
    function error(status, title) {
        common(status, API + 'Error');
        const body = pm.response.json();
        pm.test('body is an api:Error' + (title ? ' titled "' + title + '"' : ''), () => {
            pm.expect(body['@type']).to.eql('api:Error');
            pm.expect(body['api:hasTitle']).to.be.a('string');
            if (title) {
                pm.expect(body['api:hasTitle']).to.include(title);
            }
        });
    }
    function rfc1123(name) {
        pm.test(name + ' is an RFC 1123 date', () => pm.expect(header(name)).to.match(/^[A-Z][a-z]{2}, \d{2} [A-Z][a-z]{2} \d{4} \d{2}:\d{2}:\d{2} GMT$/));
    }
    function location(variable) {
        pm.test('Location names the new resource', () => pm.expect(header('Location')).to.match(/^https?:\/\//));
        pm.collectionVariables.set(variable, header('Location'));
    }
    function status(expected) {
        const body = pm.response.json();
        pm.test('request status is ' + expected, () => pm.expect(body['api:hasRequestStatus']).to.eql({'@id': 'api:' + expected}));
        pm.test('status timestamp and history only at 2.3', () => {
            if (is23) {
                pm.expect(body).to.have.property('api:hasRequestStatusSince');
            } else {
                pm.expect(body).to.not.have.property('api:hasRequestStatusSince');
                pm.expect(body).to.not.have.property('api:hasRequestStatusHistory');
            }
        });
    }
    function transitionRefused() {
        // 2.3 specifies 422 for an impossible transition; 2.2 left it open and 400 was the common reading.
        error(is23 ? 422 : 400);
    }
    JS;

/**
 * @param array<string, string> $headers
 * @return array<string, mixed>
 */
$request = static function (string $name, string $method, string $url, ?string $agent, ?string $body, string $tests, array $headers = []) use ($helpers): array {
    $all = ['Accept' => 'application/ld+json; version={{apiVersion}}'];
    if ($body !== null) {
        $all['Content-Type'] = 'application/ld+json; version={{apiVersion}}';
    }
    if ($agent !== null) {
        $all['Authorization'] = 'Bearer {{' . $agent . 'Token}}';
    }
    $all = [...$all, ...$headers];
    $headerList = [];
    foreach ($all as $key => $value) {
        $headerList[] = ['key' => $key, 'value' => $value];
    }
    $item = [
        'name' => $name,
        'event' => [['listen' => 'test', 'script' => ['type' => 'text/javascript', 'exec' => explode("\n", $helpers . "\n" . $tests)]]],
        'request' => [
            'method' => $method,
            'header' => $headerList,
            'url' => $url,
        ],
    ];
    if ($body !== null) {
        $item['request']['body'] = ['mode' => 'raw', 'raw' => $body, 'options' => ['raw' => ['language' => 'json']]];
    }

    return $item;
};

$change = static fn(string $revision, string $value): string => json_encode([
    '@context' => ['cargo' => 'https://onerecord.iata.org/ns/cargo#', 'api' => 'https://onerecord.iata.org/ns/api#', 'xsd' => 'http://www.w3.org/2001/XMLSchema#', 'api:hasDatatype' => ['@type' => 'xsd:anyURI'], 'api:p' => ['@type' => 'xsd:anyURI']],
    '@type' => 'api:Change',
    'api:hasLogisticsObject' => ['@id' => '{{pieceUri}}'],
    'api:hasDescription' => 'Compliance run: ' . $value,
    'api:hasOperation' => [[
        '@type' => 'api:Operation',
        'api:op' => ['@id' => 'api:ADD'],
        'api:s' => '{{pieceUri}}',
        'api:p' => 'https://onerecord.iata.org/ns/cargo#goodsDescription',
        'api:o' => [['@type' => 'api:OperationObject', 'api:hasDatatype' => 'http://www.w3.org/2001/XMLSchema#string', 'api:hasValue' => $value]],
    ]],
    'api:hasRevision' => ['@type' => 'xsd:positiveInteger', '@value' => $revision],
], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

$piece = $fixture('Piece.json');
$examplePiece = 'https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b7c';

$folders = [
    'Server information' => [
        $request('Server information', 'GET', '{{baseUrl}}/', 'holder', null, <<<'JS'
            common(200, API + 'ServerInformation');
            const body = pm.response.json();
            pm.test('body is api:ServerInformation with data holder and endpoint', () => {
                pm.expect(body['@type']).to.eql('api:ServerInformation');
                pm.expect(body['api:hasDataHolder']).to.be.an('object');
                pm.expect(body['api:hasServerEndpoint']).to.exist;
            });
            pm.test('the negotiated version is among the supported ones', () => {
                const versions = [].concat(body['api:hasSupportedApiVersion']).map(x => typeof x === 'string' ? x : x['@value']);
                pm.expect(versions).to.include(v);
            });
            pm.test('supported content type and language are listed', () => {
                pm.expect([].concat(body['api:hasSupportedContentType'])).to.include('application/ld+json');
                pm.expect(body['api:hasSupportedLanguage']).to.exist;
            });
            JS),
        $request('Server information needs a token', 'GET', '{{baseUrl}}/', null, null, "error(401, 'Not authenticated');"),
        $request('Unsupported media type in Accept', 'GET', '{{baseUrl}}/', 'holder', null, <<<'JS'
            pm.test('status is 406', () => pm.response.to.have.status(406));
            pm.test('Type is api:Error', () => pm.expect(header('Type')).to.eql(API + 'Error'));
            JS, ['Accept' => 'text/turtle']),
        $request('Unknown API version in Accept', 'GET', '{{baseUrl}}/', 'holder', null, <<<'JS'
            pm.test('status is 406', () => pm.response.to.have.status(406));
            pm.test('body is an api:Error', () => pm.expect(pm.response.json()['@type']).to.eql('api:Error'));
            JS, ['Accept' => 'application/ld+json; version=9.9.0']),
    ],
    'Logistics objects' => [
        $request('Create a Piece as the data holder', 'POST', '{{baseUrl}}/logistics-objects', 'holder', $piece, <<<'JS'
            common(201, CARGO + 'Piece');
            location('pieceUri');
            pm.test('no body on 201', () => pm.expect(pm.response.text()).to.eql(''));
            JS),
        $request('Create a second Piece the partner gets no access to', 'POST', '{{baseUrl}}/logistics-objects', 'holder', $piece, "common(201, CARGO + 'Piece'); location('piece2Uri');"),
        $request('A partner may not create objects', 'POST', '{{baseUrl}}/logistics-objects', 'partner', $piece, "error(403, 'Not authorized');"),
        $request('A body without a logistics object type is refused', 'POST', '{{baseUrl}}/logistics-objects', 'holder', '{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "cargo:coload": true}', "error(400, 'Invalid resource');"),
        $request('A body with an unknown property is refused', 'POST', '{{baseUrl}}/logistics-objects', 'holder', '{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:Piece", "cargo:colour": "red"}', "error(400, 'Invalid resource');"),
        $request('Read the Piece', 'GET', '{{pieceUri}}', 'holder', null, <<<'JS'
            common(200, CARGO + 'Piece');
            pm.test('Revision and Latest-Revision are 1', () => { pm.expect(header('Revision')).to.eql('1'); pm.expect(header('Latest-Revision')).to.eql('1'); });
            rfc1123('Last-Modified');
            const body = pm.response.json();
            pm.test('body carries @id, revision properties and the posted data', () => {
                pm.expect(body['@id']).to.eql(pm.collectionVariables.get('pieceUri'));
                pm.expect(body['@type']).to.eql('cargo:Piece');
                pm.expect(body['api:hasRevision']).to.eql(1);
                pm.expect(body['api:hasLatestRevision']).to.eql(1);
                pm.expect(body['cargo:coload']).to.eql(false);
            });
            JS),
        $request('HEAD the Piece', 'HEAD', '{{pieceUri}}', 'holder', null, <<<'JS'
            common(200, CARGO + 'Piece');
            pm.test('Revision headers without a body', () => { pm.expect(header('Latest-Revision')).to.eql('1'); pm.expect(pm.response.text()).to.eql(''); });
            JS),
        $request('An unknown object is 404', 'GET', '{{baseUrl}}/logistics-objects/does-not-exist', 'holder', null, "error(404, 'Resource not found');"),
        $request('A partner without permission is 403', 'GET', '{{pieceUri}}', 'partner', null, "error(403, 'Not authorized');"),
        $request('A body in another content type is 415', 'POST', '{{baseUrl}}/logistics-objects', 'holder', '<rdf/>', "error(415, 'Unsupported');", ['Content-Type' => 'text/turtle']),
    ],
    'Access delegations' => [
        $request('The partner requests access to the Piece', 'POST', '{{baseUrl}}/access-delegations', 'partner', $fixture('AccessDelegation_example1.json', [
            'https://1r.example.com/logistics-objects/Airline_XYZ' => '{{partnerAgent}}',
            $examplePiece => '{{pieceUri}}',
            '"api:hasPermission": [
        {
            "@id": "api:GET_LOGISTICS_OBJECT"
        }
    ]' => '"api:hasPermission": [{"@id": "api:GET_LOGISTICS_OBJECT"}, {"@id": "api:PATCH_LOGISTICS_OBJECT"}, {"@id": "api:POST_LOGISTICS_EVENT"}, {"@id": "api:GET_LOGISTICS_EVENT"}]',
        ]), "common(201, API + 'AccessDelegationRequest'); location('delegationUri');"),
        $request('The requestor reads the pending request', 'GET', '{{delegationUri}}', 'partner', null, <<<'JS'
            common(200, API + 'AccessDelegationRequest');
            status('REQUEST_PENDING');
            const body = pm.response.json();
            pm.test('the delegation is embedded', () => pm.expect(body['api:hasAccessDelegation']['@type']).to.eql('api:AccessDelegation'));
            pm.test('isRequestedBy is the partner', () => pm.expect(body['api:isRequestedBy']).to.eql({'@id': pm.variables.get('partnerAgent')}));
            JS),
        $request('Only the holder decides', 'PATCH', '{{delegationUri}}?status=REQUEST_ACCEPTED', 'partner', null, "error(403, 'Not authorized');"),
        $request('The holder accepts', 'PATCH', '{{delegationUri}}?status=REQUEST_ACCEPTED', 'holder', null, <<<'JS'
            common(204, API + 'AccessDelegationRequest');
            pm.test('no body on 204', () => pm.expect(pm.response.text()).to.eql(''));
            JS),
        $request('The partner can now read the Piece', 'GET', '{{pieceUri}}', 'partner', null, "common(200, CARGO + 'Piece');"),
        $request('The request is accepted', 'GET', '{{delegationUri}}', 'partner', null, "common(200, API + 'AccessDelegationRequest'); status('REQUEST_ACCEPTED');"),
    ],
    'Change requests' => [
        $request('The partner requests a change', 'PATCH', '{{pieceUri}}', 'partner', $change('1', 'Compliance run'), <<<'JS'
            common(201, API + 'ChangeRequest');
            location('changeUri');
            JS),
        $request('The change is pending and the object untouched', 'GET', '{{pieceUri}}', 'partner', null, "common(200, CARGO + 'Piece'); pm.test('still revision 1', () => pm.expect(header('Latest-Revision')).to.eql('1'));"),
        $request('The requestor reads the change request', 'GET', '{{changeUri}}', 'partner', null, <<<'JS'
            common(200, API + 'ChangeRequest');
            status('REQUEST_PENDING');
            const body = pm.response.json();
            pm.test('the change is embedded with its operations', () => {
                pm.expect(body['api:hasChange']['@type']).to.eql('api:Change');
                pm.expect([].concat(body['api:hasChange']['api:hasOperation'])).to.have.lengthOf(1);
            });
            rfc1123('Last-Modified');
            JS),
        $request('HEAD the change request', 'HEAD', '{{changeUri}}', 'partner', null, "common(200, API + 'ChangeRequest'); pm.test('no body', () => pm.expect(pm.response.text()).to.eql(''));"),
        $request('A stranger cannot see it', 'GET', '{{changeUri}}', null, null, 'error(401);'),
        $request('The holder accepts the change', 'PATCH', '{{changeUri}}?status=REQUEST_ACCEPTED', 'holder', null, "common(204, API + 'ChangeRequest');"),
        $request('The object is at revision 2 with the new value', 'GET', '{{pieceUri}}', 'partner', null, <<<'JS'
            common(200, CARGO + 'Piece');
            pm.test('Revision 2', () => { pm.expect(header('Revision')).to.eql('2'); pm.expect(header('Latest-Revision')).to.eql('2'); });
            const body = pm.response.json();
            pm.test('the change is applied', () => { pm.expect(body['api:hasRevision']).to.eql(2); pm.expect(body['cargo:goodsDescription']).to.eql('Compliance run'); });
            JS),
        $request('The audit trail lists the change request', 'GET', '{{pieceUri}}/audit-trail', 'partner', null, <<<'JS'
            common(200, API + 'AuditTrail');
            const body = pm.response.json();
            pm.test('api:AuditTrail with latest revision 2 and one request', () => {
                pm.expect(body['@type']).to.eql('api:AuditTrail');
                pm.expect(body['api:hasLatestRevision']['@value']).to.eql('2');
                pm.expect([].concat(body['api:hasActionRequest'])).to.have.lengthOf(1);
            });
            JS),
        $request('An accepted change cannot be revoked', 'DELETE', '{{changeUri}}', 'partner', null, 'transitionRefused();'),
        $request('A change against a stale revision is accepted as a request', 'PATCH', '{{pieceUri}}', 'partner', $change('1', 'Stale'), "common(201, API + 'ChangeRequest'); location('staleChangeUri');"),
        $request('Accepting it fails because the revision moved on', 'PATCH', '{{staleChangeUri}}?status=REQUEST_ACCEPTED', 'holder', null, "common(204, API + 'ChangeRequest');"),
        $request('The stale request is REQUEST_FAILED with an error', 'GET', '{{staleChangeUri}}', 'partner', null, <<<'JS'
            common(200, API + 'ChangeRequest');
            status('REQUEST_FAILED');
            pm.test('api:hasError explains', () => pm.expect([].concat(pm.response.json()['api:hasError'])).to.have.lengthOf.at.least(1));
            JS),
        $request('Another change for the holder to reject', 'PATCH', '{{pieceUri}}', 'partner', $change('2', 'To be rejected'), "common(201, API + 'ChangeRequest'); location('rejectedChangeUri');"),
        $request('The holder rejects', 'PATCH', '{{rejectedChangeUri}}?status=REQUEST_REJECTED', 'holder', null, "common(204, API + 'ChangeRequest');"),
        $request('The request is REQUEST_REJECTED', 'GET', '{{rejectedChangeUri}}', 'partner', null, "common(200, API + 'ChangeRequest'); status('REQUEST_REJECTED');"),
        $request('Rejecting twice is refused', 'PATCH', '{{rejectedChangeUri}}?status=REQUEST_REJECTED', 'holder', null, 'transitionRefused();'),
        $request('Another change the partner revokes', 'PATCH', '{{pieceUri}}', 'partner', $change('2', 'To be revoked'), "common(201, API + 'ChangeRequest'); location('revokedChangeUri');"),
        $request('The requestor revokes', 'DELETE', '{{revokedChangeUri}}', 'partner', null, "common(204, API + 'ChangeRequest');"),
        $request('The request is REQUEST_REVOKED', 'GET', '{{revokedChangeUri}}', 'partner', null, <<<'JS'
            common(200, API + 'ChangeRequest');
            status('REQUEST_REVOKED');
            pm.test('isRevokedBy is the partner', () => pm.expect(pm.response.json()['api:isRevokedBy']).to.eql({'@id': pm.variables.get('partnerAgent')}));
            JS),
        $request('A change naming another object is refused', 'PATCH', '{{pieceUri}}', 'partner', str_replace('"@id": "{{pieceUri}}"', '"@id": "{{piece2Uri}}"', $change('2', 'Wrong target')), "error(400, 'Invalid resource');"),
        $request('A change against a future revision is refused', 'PATCH', '{{pieceUri}}', 'partner', $change('9', 'Future'), 'error(422);'),
        $request('An unknown status value is refused', 'PATCH', '{{changeUri}}?status=REQUEST_WHATEVER', 'holder', null, "error(400, 'Invalid query parameter');"),
        $request('An unknown action request is 404', 'GET', '{{baseUrl}}/action-requests/does-not-exist', 'partner', null, "error(404, 'Resource not found');"),
    ],
    'Logistics events' => [
        $request('The partner posts an event', 'POST', '{{pieceUri}}/logistics-events', 'partner', $fixture('LogisticsEvent.json', ['https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b3c' => '{{pieceUri}}']), <<<'JS'
            common(201, CARGO + 'LogisticsEvent');
            location('eventUri');
            pm.test('the event lives under the object', () => pm.expect(header('Location')).to.include(pm.collectionVariables.get('pieceUri') + '/logistics-events/'));
            JS),
        $request('Read the event', 'GET', '{{eventUri}}', 'partner', null, <<<'JS'
            common(200, CARGO + 'LogisticsEvent');
            rfc1123('Last-Modified');
            const body = pm.response.json();
            pm.test('the event body', () => {
                pm.expect(body['@id']).to.eql(pm.collectionVariables.get('eventUri'));
                pm.expect(body['@type']).to.eql('cargo:LogisticsEvent');
                pm.expect(body['cargo:eventCode']).to.eql({'@id': 'https://onerecord.iata.org/ns/code-lists/StatusCode#DEP'});
            });
            JS),
        $request('List the events', 'GET', '{{pieceUri}}/logistics-events', 'partner', null, <<<'JS'
            common(200, API + 'Collection');
            rfc1123('Last-Modified');
            const body = pm.response.json();
            pm.test('an api:Collection with one item', () => { pm.expect(body['@type']).to.eql('api:Collection'); pm.expect(body['api:hasTotalItems']).to.eql(1); });
            JS),
        $request('HEAD the event list', 'HEAD', '{{pieceUri}}/logistics-events', 'partner', null, "common(200, API + 'Collection'); rfc1123('Last-Modified'); pm.test('no body', () => pm.expect(pm.response.text()).to.eql(''));"),
        $request('Filter by event code, matching', 'GET', '{{pieceUri}}/logistics-events?event-code=DEP', 'partner', null, "common(200, API + 'Collection'); pm.test('one item', () => pm.expect(pm.response.json()['api:hasTotalItems']).to.eql(1));"),
        $request('Filter by event code, not matching', 'GET', '{{pieceUri}}/logistics-events?event-code=ARR', 'partner', null, "common(200, API + 'Collection'); pm.test('no items', () => pm.expect(pm.response.json()['api:hasTotalItems']).to.eql(0));"),
        $request('An invalid sort parameter is refused', 'GET', '{{pieceUri}}/logistics-events?sort=bogus', 'partner', null, "error(400, 'Invalid query parameter');"),
        $request('A body that is not a LogisticsEvent is refused', 'POST', '{{pieceUri}}/logistics-events', 'partner', $piece, "error(400, 'Invalid resource');"),
        $request('An unknown event is 404', 'GET', '{{pieceUri}}/logistics-events/does-not-exist', 'partner', null, "error(404, 'Resource not found');"),
        $request('Events on an object without permission are 403', 'POST', '{{piece2Uri}}/logistics-events', 'partner', $fixture('LogisticsEvent.json', ['https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b3c' => '{{piece2Uri}}']), "error(403, 'Not authorized');"),
        $request('Bulk events (API 2.3 only)', 'POST', '{{baseUrl}}/logistics-events', 'partner', $fixture('MultipleEvents_example1.json', [
            'https://1r.example.com/logistics-objects/78fee8e2-772b-4fff-bede-cfda67900d3b' => '{{pieceUri}}',
            'https://1r.example.com/logistics-objects/f166f1fa-ea2d-4c01-b1d3-cde8bb973757' => '{{baseUrl}}/logistics-objects/does-not-exist',
            'https://1r.example.com/logistics-objects/22f07756-9d67-4622-adab-c62d1ebde115' => '{{piece2Uri}}',
        ]), <<<'JS'
            if (!is23) {
                error(404, 'Resource not found');
            } else {
                common(207, API + 'MultiStatusResponse');
                const body = pm.response.json();
                pm.test('one created, two failed, with per-object results', () => {
                    pm.expect(body['@type']).to.eql('api:MultiStatusResponse');
                    pm.expect(body['api:hasTotalItems']).to.eql(3);
                    pm.expect(body['api:hasTotalCreated']).to.eql(1);
                    pm.expect(body['api:hasTotalFailed']).to.eql(2);
                    pm.expect(body['api:hasCreationResult'].map(r => r['api:hasHTTPStatus'])).to.have.members([201, 404, 403]);
                });
            }
            JS),
    ],
    'Verification' => [
        $request('The partner flags a problem', 'POST', '{{pieceUri}}', 'partner', $fixture('Verification.json', [$examplePiece => '{{pieceUri}}']), "common(201, API + 'VerificationRequest'); location('verificationUri');"),
        $request('The request carries the errors', 'GET', '{{verificationUri}}', 'partner', null, <<<'JS'
            common(200, API + 'VerificationRequest');
            status('REQUEST_PENDING');
            pm.test('two api:Error entries', () => pm.expect([].concat(pm.response.json()['api:hasVerification']['api:hasError'])).to.have.lengthOf(2));
            JS),
        $request('A verification cannot be accepted', 'PATCH', '{{verificationUri}}?status=REQUEST_ACCEPTED', 'holder', null, 'transitionRefused();'),
        $request('The holder acknowledges', 'PATCH', '{{verificationUri}}?status=REQUEST_ACKNOWLEDGED', 'holder', null, "common(204, API + 'VerificationRequest');"),
        $request('The request is REQUEST_ACKNOWLEDGED', 'GET', '{{verificationUri}}', 'partner', null, "common(200, API + 'VerificationRequest'); status('REQUEST_ACKNOWLEDGED');"),
    ],
    'Subscriptions' => [
        $request('A publisher asks without a topic', 'GET', '{{baseUrl}}/subscriptions', 'partner', null, "error(400, 'Invalid query parameter');"),
        $request('A publisher asks about an object', 'GET', '{{baseUrl}}/subscriptions?topicType=LOGISTICS_OBJECT_IDENTIFIER&topic={{pieceUri}}', 'partner', null, <<<'JS'
            pm.test('status is 200', () => pm.response.to.have.status(200));
            pm.test('answer is a Subscription or a Collection of them', () => pm.expect(header('Type')).to.be.oneOf([API + 'Subscription', API + 'Collection']));
            JS),
        $request('The partner subscribes to the Piece', 'POST', '{{baseUrl}}/subscriptions', 'partner', $fixture('Subscription_example1.json', [
            'https://1r.example.com/logistics-objects/957e2622-9d31-493b-8b8f-3c805064dbda' => '{{partnerAgent}}',
            $examplePiece => '{{pieceUri}}',
        ]), "common(201, API + 'SubscriptionRequest'); location('subscriptionUri');"),
        $request('The subscription request is pending', 'GET', '{{subscriptionUri}}', 'partner', null, <<<'JS'
            common(200, API + 'SubscriptionRequest');
            status('REQUEST_PENDING');
            pm.test('the subscription is embedded', () => pm.expect(pm.response.json()['api:hasSubscription']['@type']).to.eql('api:Subscription'));
            JS),
        $request('The holder accepts the subscription', 'PATCH', '{{subscriptionUri}}?status=REQUEST_ACCEPTED', 'holder', null, "common(204, API + 'SubscriptionRequest');"),
        $request('An accepted subscription can be revoked', 'DELETE', '{{subscriptionUri}}', 'partner', null, "common(204, API + 'SubscriptionRequest');"),
        $request('A subscription to an unknown object is refused', 'POST', '{{baseUrl}}/subscriptions', 'partner', $fixture('Subscription_example1.json', [
            'https://1r.example.com/logistics-objects/957e2622-9d31-493b-8b8f-3c805064dbda' => '{{partnerAgent}}',
            $examplePiece => '{{baseUrl}}/logistics-objects/does-not-exist',
        ]), "error(400, 'Invalid resource');"),
    ],
    'Notifications' => [
        $request('A notification is accepted', 'POST', '{{baseUrl}}/notifications', 'partner', $fixture('Notification_example1.json'), "common(204); pm.test('no body', () => pm.expect(pm.response.text()).to.eql(''));"),
        $request('A notification without an event type is refused', 'POST', '{{baseUrl}}/notifications', 'partner', '{"@context": {"api": "https://onerecord.iata.org/ns/api#"}, "@type": "api:Notification"}', 'error(400);'),
    ],
    'Errors' => [
        $request('An unknown path is 404', 'GET', '{{baseUrl}}/nothing-here', 'holder', null, "error(404, 'Resource not found');"),
        $request('A wrong method is 405 with Allow', 'PUT', '{{baseUrl}}/', 'holder', null, "error(405, 'Method not allowed'); pm.test('Allow lists the methods', () => pm.expect(header('Allow')).to.include('GET'));"),
        $request('Malformed JSON is 400', 'POST', '{{baseUrl}}/logistics-objects', 'holder', '{not json', "error(400, 'Invalid body request');"),
    ],
];

$collection = [
    'info' => [
        'name' => 'ONE Record API compliance (lambda-twelve/one-record)',
        'description' => 'Generated by tests/Compliance/collection.php from the specification examples and MUST tables. Run once per API version.',
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        ['key' => 'apiVersion', 'value' => '2.3.0'],
    ],
    'item' => array_map(static fn(string $name, array $items): array => ['name' => $name, 'item' => $items], array_keys($folders), array_values($folders)),
];

echo json_encode($collection, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
