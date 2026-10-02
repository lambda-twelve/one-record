<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated;

/** Structural facts about the ontology, for runtime validation. Read through Vocabulary, not directly. */
final class ApiSchema
{
    public const string NAMESPACE = 'https://onerecord.iata.org/ns/api#';

    /** @var list<string> versions merged into this schema, oldest first */
    public const array VERSIONS = [
        '2.2.0',
        '2.3.0',
    ];

    /**
     * Keyed by class IRI. `properties` maps the property IRIs this class restricts (its own, not inherited) to the version that attached them.
     *
     * @var array<string, array{name: string, parents: list<string>, properties: array<string, string>, since: string, deprecatedIn: ?string, removedIn: ?string}>
     */
    public const array CLASSES = [
        'https://onerecord.iata.org/ns/api#AccessDelegation' => [
            'name' => 'AccessDelegation',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#expiresAt' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasDescription' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsObject' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasPermission' => '2.2.0',
                'https://onerecord.iata.org/ns/api#isRequestedFor' => '2.2.0',
                'https://onerecord.iata.org/ns/api#notifyRequestStatusChange' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#AccessDelegationRequest' => [
            'name' => 'AccessDelegationRequest',
            'parents' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasAccessDelegation' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ActionRequest' => [
            'name' => 'ActionRequest',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasError' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasRequestStatus' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasRequestStatusHistory' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasRequestStatusSince' => '2.3.0',
                'https://onerecord.iata.org/ns/api#isRequestedAt' => '2.2.0',
                'https://onerecord.iata.org/ns/api#isRequestedBy' => '2.2.0',
                'https://onerecord.iata.org/ns/api#isRevokedAt' => '2.2.0',
                'https://onerecord.iata.org/ns/api#isRevokedBy' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#AuditTrail' => [
            'name' => 'AuditTrail',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasActionRequest' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasLatestRevision' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Change' => [
            'name' => 'Change',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasDescription' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsObject' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasOperation' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasRevision' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasVerificationRequest' => '2.2.0',
                'https://onerecord.iata.org/ns/api#notifyRequestStatusChange' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ChangeRequest' => [
            'name' => 'ChangeRequest',
            'parents' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasChange' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Collection' => [
            'name' => 'Collection',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasItem' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasTotalItems' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Error' => [
            'name' => 'Error',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasErrorDetail' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSeverity' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasTitle' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ErrorDetail' => [
            'name' => 'ErrorDetail',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasCode' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasMessage' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasProperty' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasResource' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#EventCreationResult' => [
            'name' => 'EventCreationResult',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasError' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasHTTPStatus' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsEvent' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsObject' => '2.3.0',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#MultiStatusResponse' => [
            'name' => 'MultiStatusResponse',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasCreationResult' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasTotalCreated' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasTotalFailed' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasTotalItems' => '2.3.0',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Notification' => [
            'name' => 'Notification',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasChangedProperty' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasEventType' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsEvent' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsObject' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsObjectType' => '2.2.0',
                'https://onerecord.iata.org/ns/api#isTriggeredBy' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#NotificationEventType' => [
            'name' => 'NotificationEventType',
            'parents' => [],
            'properties' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Operation' => [
            'name' => 'Operation',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#o' => '2.2.0',
                'https://onerecord.iata.org/ns/api#op' => '2.2.0',
                'https://onerecord.iata.org/ns/api#p' => '2.2.0',
                'https://onerecord.iata.org/ns/api#s' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#OperationObject' => [
            'name' => 'OperationObject',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasDatatype' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasValue' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#PatchOperation' => [
            'name' => 'PatchOperation',
            'parents' => [],
            'properties' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Permission' => [
            'name' => 'Permission',
            'parents' => [],
            'properties' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#RequestStatus' => [
            'name' => 'RequestStatus',
            'parents' => [],
            'properties' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#RequestStatusEntry' => [
            'name' => 'RequestStatusEntry',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasRequestStatus' => '2.3.0',
                'https://onerecord.iata.org/ns/api#hasRequestStatusSince' => '2.3.0',
                'https://onerecord.iata.org/ns/api#isChangedBy' => '2.3.0',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ServerInformation' => [
            'name' => 'ServerInformation',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasDataHolder' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasServerEndpoint' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSupportedApiVersion' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSupportedContentType' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSupportedEncoding' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSupportedLanguage' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSupportedOntology' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSupportedOntologyVersion' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Severity' => [
            'name' => 'Severity',
            'parents' => [],
            'properties' => [],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Subscription' => [
            'name' => 'Subscription',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#expiresAt' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasContentType' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasDescription' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasSubscriber' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasTopic' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasTopicType' => '2.2.0',
                'https://onerecord.iata.org/ns/api#includeSubscriptionEventType' => '2.2.0',
                'https://onerecord.iata.org/ns/api#notifyRequestStatusChange' => '2.2.0',
                'https://onerecord.iata.org/ns/api#sendLogisticsObjectBody' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#SubscriptionEventType' => [
            'name' => 'SubscriptionEventType',
            'parents' => [],
            'properties' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#SubscriptionRequest' => [
            'name' => 'SubscriptionRequest',
            'parents' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasSubscription' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#TopicType' => [
            'name' => 'TopicType',
            'parents' => [],
            'properties' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#Verification' => [
            'name' => 'Verification',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasError' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasLogisticsObject' => '2.2.0',
                'https://onerecord.iata.org/ns/api#hasRevision' => '2.2.0',
                'https://onerecord.iata.org/ns/api#notifyRequestStatusChange' => '2.3.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#VerificationRequest' => [
            'name' => 'VerificationRequest',
            'parents' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/api#hasVerification' => '2.2.0',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
    ];

    /**
     * Keyed by property IRI. `domains` is the declared domain(s); '*' means any class.
     *
     * @var array<string, array{name: string, kind: 'object'|'datatype', ranges: list<string>, domains: list<string>, since: string, deprecatedIn: ?string, removedIn: ?string}>
     */
    public const array PROPERTIES = [
        'https://onerecord.iata.org/ns/api#expiresAt' => [
            'name' => 'expiresAt',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AccessDelegation',
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasAccessDelegation' => [
            'name' => 'hasAccessDelegation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#AccessDelegation',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AccessDelegationRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasActionRequest' => [
            'name' => 'hasActionRequest',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AuditTrail',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasChange' => [
            'name' => 'hasChange',
            'kind' => 'object',
            'ranges' => [],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ChangeRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasChangeRequest' => [
            'name' => 'hasChangeRequest',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#ChangeRequest',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AuditTrail',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasChangedProperty' => [
            'name' => 'hasChangedProperty',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Notification',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasCode' => [
            'name' => 'hasCode',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ErrorDetail',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasContentType' => [
            'name' => 'hasContentType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasCreationResult' => [
            'name' => 'hasCreationResult',
            'kind' => 'object',
            'ranges' => [],
            'domains' => [],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasDataHolder' => [
            'name' => 'hasDataHolder',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasDatatype' => [
            'name' => 'hasDatatype',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#OperationObject',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasDescription' => [
            'name' => 'hasDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AccessDelegation',
                'https://onerecord.iata.org/ns/api#Change',
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasError' => [
            'name' => 'hasError',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#Error',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasErrorDetail' => [
            'name' => 'hasErrorDetail',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#ErrorDetail',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Error',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasEventType' => [
            'name' => 'hasEventType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Notification',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasHTTPStatus' => [
            'name' => 'hasHTTPStatus',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#nonNegativeInteger',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#EventCreationResult',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasItem' => [
            'name' => 'hasItem',
            'kind' => 'object',
            'ranges' => [],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Collection',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasLatestRevision' => [
            'name' => 'hasLatestRevision',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#positiveInteger',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AuditTrail',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasLogisticsEvent' => [
            'name' => 'hasLogisticsEvent',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'domains' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasLogisticsObject' => [
            'name' => 'hasLogisticsObject',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'domains' => [],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasLogisticsObjectType' => [
            'name' => 'hasLogisticsObjectType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Notification',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasMessage' => [
            'name' => 'hasMessage',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ErrorDetail',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasOperation' => [
            'name' => 'hasOperation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#Operation',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Change',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasPermission' => [
            'name' => 'hasPermission',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#Permission',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AccessDelegation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasProperty' => [
            'name' => 'hasProperty',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ErrorDetail',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasRequestStatus' => [
            'name' => 'hasRequestStatus',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#RequestStatus',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasRequestStatusHistory' => [
            'name' => 'hasRequestStatusHistory',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#RequestStatusEntry',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasRequestStatusSince' => [
            'name' => 'hasRequestStatusSince',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
                'https://onerecord.iata.org/ns/api#RequestStatusEntry',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasResource' => [
            'name' => 'hasResource',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ErrorDetail',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasRevision' => [
            'name' => 'hasRevision',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#positiveInteger',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ChangeRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasServerEndpoint' => [
            'name' => 'hasServerEndpoint',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSeverity' => [
            'name' => 'hasSeverity',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#Severity',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Error',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSubscriber' => [
            'name' => 'hasSubscriber',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSubscription' => [
            'name' => 'hasSubscription',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#SubscriptionRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSupportedApiVersion' => [
            'name' => 'hasSupportedApiVersion',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSupportedContentType' => [
            'name' => 'hasSupportedContentType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSupportedEncoding' => [
            'name' => 'hasSupportedEncoding',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSupportedLanguage' => [
            'name' => 'hasSupportedLanguage',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSupportedOntology' => [
            'name' => 'hasSupportedOntology',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasSupportedOntologyVersion' => [
            'name' => 'hasSupportedOntologyVersion',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ServerInformation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasTitle' => [
            'name' => 'hasTitle',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Error',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasTopic' => [
            'name' => 'hasTopic',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Notification',
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasTopicType' => [
            'name' => 'hasTopicType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#TopicType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasTotalCreated' => [
            'name' => 'hasTotalCreated',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#nonNegativeInteger',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#MultiStatusResponse',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasTotalFailed' => [
            'name' => 'hasTotalFailed',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#nonNegativeInteger',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#MultiStatusResponse',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasTotalItems' => [
            'name' => 'hasTotalItems',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#nonNegativeInteger',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Collection',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasValue' => [
            'name' => 'hasValue',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#OperationObject',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasVerification' => [
            'name' => 'hasVerification',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#Verification',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#VerificationRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#hasVerificationRequest' => [
            'name' => 'hasVerificationRequest',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#VerificationRequest',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Change',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#includeSubscriptionEventType' => [
            'name' => 'includeSubscriptionEventType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#SubscriptionEventType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#isChangedBy' => [
            'name' => 'isChangedBy',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#RequestStatusEntry',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#isRequestedAt' => [
            'name' => 'isRequestedAt',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#isRequestedBy' => [
            'name' => 'isRequestedBy',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#isRequestedFor' => [
            'name' => 'isRequestedFor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AccessDelegation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#isRevokedAt' => [
            'name' => 'isRevokedAt',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#isRevokedBy' => [
            'name' => 'isRevokedBy',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#isTriggeredBy' => [
            'name' => 'isTriggeredBy',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#ActionRequest',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Notification',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#notifyRequestStatusChange' => [
            'name' => 'notifyRequestStatusChange',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#AccessDelegation',
                'https://onerecord.iata.org/ns/api#Change',
                'https://onerecord.iata.org/ns/api#Subscription',
                'https://onerecord.iata.org/ns/api#Verification',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#o' => [
            'name' => 'o',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#OperationObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Operation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#op' => [
            'name' => 'op',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/api#PatchOperation',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Operation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#p' => [
            'name' => 'p',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#anyURI',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Operation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#s' => [
            'name' => 's',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Operation',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#sendLogisticsObjectBody' => [
            'name' => 'sendLogisticsObjectBody',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/api#Subscription',
            ],
            'since' => '2.2.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#usesOrchestration' => [
            'name' => 'usesOrchestration',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'since' => '2.3.0',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
    ];

    /**
     * Keyed by individual IRI.
     *
     * @var array<string, array{name: string, types: list<string>, since: string, removedIn: ?string}>
     */
    public const array INDIVIDUALS = [
        'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_ACCEPTED' => [
            'name' => 'ACCESS_DELEGATION_REQUEST_ACCEPTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_FAILED' => [
            'name' => 'ACCESS_DELEGATION_REQUEST_FAILED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_PENDING' => [
            'name' => 'ACCESS_DELEGATION_REQUEST_PENDING',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_REJECTED' => [
            'name' => 'ACCESS_DELEGATION_REQUEST_REJECTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_REVOKED' => [
            'name' => 'ACCESS_DELEGATION_REQUEST_REVOKED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ADD' => [
            'name' => 'ADD',
            'types' => [
                'https://onerecord.iata.org/ns/api#PatchOperation',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_ACCEPTED' => [
            'name' => 'CHANGE_REQUEST_ACCEPTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_FAILED' => [
            'name' => 'CHANGE_REQUEST_FAILED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_PENDING' => [
            'name' => 'CHANGE_REQUEST_PENDING',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_REJECTED' => [
            'name' => 'CHANGE_REQUEST_REJECTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_REVOKED' => [
            'name' => 'CHANGE_REQUEST_REVOKED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#DELETE' => [
            'name' => 'DELETE',
            'types' => [
                'https://onerecord.iata.org/ns/api#PatchOperation',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#ERROR' => [
            'name' => 'ERROR',
            'types' => [
                'https://onerecord.iata.org/ns/api#Severity',
            ],
            'since' => '2.3.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#GET_LOGISTICS_EVENT' => [
            'name' => 'GET_LOGISTICS_EVENT',
            'types' => [
                'https://onerecord.iata.org/ns/api#Permission',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#GET_LOGISTICS_OBJECT' => [
            'name' => 'GET_LOGISTICS_OBJECT',
            'types' => [
                'https://onerecord.iata.org/ns/api#Permission',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#LOGISTICS_EVENT_RECEIVED' => [
            'name' => 'LOGISTICS_EVENT_RECEIVED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
                'https://onerecord.iata.org/ns/api#SubscriptionEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_ACCESS_GRANTED' => [
            'name' => 'LOGISTICS_OBJECT_ACCESS_GRANTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_AVAILABLE' => [
            'name' => 'LOGISTICS_OBJECT_AVAILABLE',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_CREATED' => [
            'name' => 'LOGISTICS_OBJECT_CREATED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
                'https://onerecord.iata.org/ns/api#SubscriptionEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_IDENTIFIER' => [
            'name' => 'LOGISTICS_OBJECT_IDENTIFIER',
            'types' => [
                'https://onerecord.iata.org/ns/api#TopicType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_TYPE' => [
            'name' => 'LOGISTICS_OBJECT_TYPE',
            'types' => [
                'https://onerecord.iata.org/ns/api#TopicType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_UPDATED' => [
            'name' => 'LOGISTICS_OBJECT_UPDATED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
                'https://onerecord.iata.org/ns/api#SubscriptionEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#PATCH_LOGISTICS_OBJECT' => [
            'name' => 'PATCH_LOGISTICS_OBJECT',
            'types' => [
                'https://onerecord.iata.org/ns/api#Permission',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#POST_LOGISTICS_EVENT' => [
            'name' => 'POST_LOGISTICS_EVENT',
            'types' => [
                'https://onerecord.iata.org/ns/api#Permission',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#REQUEST_ACCEPTED' => [
            'name' => 'REQUEST_ACCEPTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#RequestStatus',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#REQUEST_ACKNOWLEDGED' => [
            'name' => 'REQUEST_ACKNOWLEDGED',
            'types' => [
                'https://onerecord.iata.org/ns/api#RequestStatus',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#REQUEST_FAILED' => [
            'name' => 'REQUEST_FAILED',
            'types' => [
                'https://onerecord.iata.org/ns/api#RequestStatus',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#REQUEST_PENDING' => [
            'name' => 'REQUEST_PENDING',
            'types' => [
                'https://onerecord.iata.org/ns/api#RequestStatus',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#REQUEST_REJECTED' => [
            'name' => 'REQUEST_REJECTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#RequestStatus',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#REQUEST_REVOKED' => [
            'name' => 'REQUEST_REVOKED',
            'types' => [
                'https://onerecord.iata.org/ns/api#RequestStatus',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_ACCEPTED' => [
            'name' => 'SUBSCRIPTION_REQUEST_ACCEPTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_FAILED' => [
            'name' => 'SUBSCRIPTION_REQUEST_FAILED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_PENDING' => [
            'name' => 'SUBSCRIPTION_REQUEST_PENDING',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_REJECTED' => [
            'name' => 'SUBSCRIPTION_REQUEST_REJECTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_REVOKED' => [
            'name' => 'SUBSCRIPTION_REQUEST_REVOKED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_ACKNOWLEDGED' => [
            'name' => 'VERIFICATION_REQUEST_ACKNOWLEDGED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_FAILED' => [
            'name' => 'VERIFICATION_REQUEST_FAILED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_PENDING' => [
            'name' => 'VERIFICATION_REQUEST_PENDING',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_REJECTED' => [
            'name' => 'VERIFICATION_REQUEST_REJECTED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_REVOKED' => [
            'name' => 'VERIFICATION_REQUEST_REVOKED',
            'types' => [
                'https://onerecord.iata.org/ns/api#NotificationEventType',
            ],
            'since' => '2.2.0',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/api#WARNING' => [
            'name' => 'WARNING',
            'types' => [
                'https://onerecord.iata.org/ns/api#Severity',
            ],
            'since' => '2.3.0',
            'removedIn' => null,
        ],
    ];
}
