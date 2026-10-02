<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated;

/** Term IRIs of the ONE Record API ontology: classes, properties and named individuals. */
final class Api
{
    public const string NAMESPACE = 'https://onerecord.iata.org/ns/api#';

    // Classes

    /** Access to a Logistics Object delegated to an Organization */
    public const string AccessDelegation = 'https://onerecord.iata.org/ns/api#AccessDelegation';

    /** Delegation Request to 3rd parties */
    public const string AccessDelegationRequest = 'https://onerecord.iata.org/ns/api#AccessDelegationRequest';

    /**
     * Superclass for all kinds of requests (i.e someone requsted something (e.g. subscription, access,
     * etc.) at a publisher/holder of a logistics object)
     */
    public const string ActionRequest = 'https://onerecord.iata.org/ns/api#ActionRequest';

    /** Audit trail of a Logistics Object */
    public const string AuditTrail = 'https://onerecord.iata.org/ns/api#AuditTrail';

    public const string Change = 'https://onerecord.iata.org/ns/api#Change';

    /** Change Request containing updates on a Logistics Object */
    public const string ChangeRequest = 'https://onerecord.iata.org/ns/api#ChangeRequest';

    /**
     * Used as response of endpoints returning a collection of more than one graph, i.e. more than one not
     * linked subjects.
     */
    public const string Collection = 'https://onerecord.iata.org/ns/api#Collection';

    /** Error model */
    public const string Error = 'https://onerecord.iata.org/ns/api#Error';

    /** Error details that belong to an error */
    public const string ErrorDetail = 'https://onerecord.iata.org/ns/api#ErrorDetail';

    /**
     * Contains the outcome of logistics event creation requests processed through a multiple-events
     * creation API.
     *
     * @since 2.3.0
     */
    public const string EventCreationResult = 'https://onerecord.iata.org/ns/api#EventCreationResult';

    /**
     * Container object that summarizes the results of multiple operations
     *
     * @since 2.3.0
     */
    public const string MultiStatusResponse = 'https://onerecord.iata.org/ns/api#MultiStatusResponse';

    /** Notification sent by the publisher to the subscriber */
    public const string Notification = 'https://onerecord.iata.org/ns/api#Notification';

    public const string NotificationEventType = 'https://onerecord.iata.org/ns/api#NotificationEventType';

    /** Operation Request contained in the PATCH body */
    public const string Operation = 'https://onerecord.iata.org/ns/api#Operation';

    /** Object to modify in the PATCH request */
    public const string OperationObject = 'https://onerecord.iata.org/ns/api#OperationObject';

    public const string PatchOperation = 'https://onerecord.iata.org/ns/api#PatchOperation';

    public const string Permission = 'https://onerecord.iata.org/ns/api#Permission';

    public const string RequestStatus = 'https://onerecord.iata.org/ns/api#RequestStatus';

    /**
     * Entry representing an historical request status and the date and time from which it applied
     *
     * @since 2.3.0
     */
    public const string RequestStatusEntry = 'https://onerecord.iata.org/ns/api#RequestStatusEntry';

    /** Information about the ONE Record server */
    public const string ServerInformation = 'https://onerecord.iata.org/ns/api#ServerInformation';

    /** @since 2.3.0 */
    public const string Severity = 'https://onerecord.iata.org/ns/api#Severity';

    /** Subscription information sent to the publisher */
    public const string Subscription = 'https://onerecord.iata.org/ns/api#Subscription';

    public const string SubscriptionEventType = 'https://onerecord.iata.org/ns/api#SubscriptionEventType';

    /**
     * SubscriptionRequest initiated by subscribers to publisher (data holder) for themselves or for a
     * third party subscriber.
     */
    public const string SubscriptionRequest = 'https://onerecord.iata.org/ns/api#SubscriptionRequest';

    public const string TopicType = 'https://onerecord.iata.org/ns/api#TopicType';

    public const string Verification = 'https://onerecord.iata.org/ns/api#Verification';

    public const string VerificationRequest = 'https://onerecord.iata.org/ns/api#VerificationRequest';


    // Properties

    /**
     * Expiry date as date time of the subscription information that supports caching but does not require
     * the ONE Record client to store the datetime when the Subscription object was received; if not given:
     * the information does not expire
     */
    public const string expiresAt = 'https://onerecord.iata.org/ns/api#expiresAt';

    public const string hasAccessDelegation = 'https://onerecord.iata.org/ns/api#hasAccessDelegation';

    /** Link any type of Action Request */
    public const string hasActionRequest = 'https://onerecord.iata.org/ns/api#hasActionRequest';

    /** Contains submitted Change object */
    public const string hasChange = 'https://onerecord.iata.org/ns/api#hasChange';

    /** Recorded change requests in the Audit Trail of a Logistics Object */
    public const string hasChangeRequest = 'https://onerecord.iata.org/ns/api#hasChangeRequest';

    /**
     * List of all changed properties as IRIs after a ChangeRequest was successfully applied, e.g.
     * [https://onerecord.iata.org/ns/cargo#hasVolumetricWeight,
     * https://onerecord.iata.org/ns/cargo/#hasGoodsDescription]
     */
    public const string hasChangedProperty = 'https://onerecord.iata.org/ns/api#hasChangedProperty';

    /**
     * Error code is a numeric or alphanumeric code that can be used to determine the source of the error
     * and why it occured.
     */
    public const string hasCode = 'https://onerecord.iata.org/ns/api#hasCode';

    /** Content types that the subscriber wants to receive in the notifications, e.g. application/ld+json */
    public const string hasContentType = 'https://onerecord.iata.org/ns/api#hasContentType';

    /**
     * Links to the list of Creation Results
     *
     * @since 2.3.0
     */
    public const string hasCreationResult = 'https://onerecord.iata.org/ns/api#hasCreationResult';

    /** The data holder of the servers data. */
    public const string hasDataHolder = 'https://onerecord.iata.org/ns/api#hasDataHolder';

    /** Data type of the field to update, must be a valid URI, e.g. http://www.w3.org/2001/XMLSchema#int */
    public const string hasDatatype = 'https://onerecord.iata.org/ns/api#hasDatatype';

    /** Reason for the request (optional) */
    public const string hasDescription = 'https://onerecord.iata.org/ns/api#hasDescription';

    /** Error object(s) if the processing was not successful */
    public const string hasError = 'https://onerecord.iata.org/ns/api#hasError';

    /** Error details */
    public const string hasErrorDetail = 'https://onerecord.iata.org/ns/api#hasErrorDetail';

    public const string hasEventType = 'https://onerecord.iata.org/ns/api#hasEventType';

    /**
     * Contains the HTTP status of the request
     *
     * @since 2.3.0
     */
    public const string hasHTTPStatus = 'https://onerecord.iata.org/ns/api#hasHTTPStatus';

    /** Item that is contained in a collection */
    public const string hasItem = 'https://onerecord.iata.org/ns/api#hasItem';

    /** Latest revision of the Logistics Object. Starting with revision 0 */
    public const string hasLatestRevision = 'https://onerecord.iata.org/ns/api#hasLatestRevision';

    /** A reference to a cargo LogisticsEvent */
    public const string hasLogisticsEvent = 'https://onerecord.iata.org/ns/api#hasLogisticsEvent';

    /** A reference to a cargo:LogisticsObject. */
    public const string hasLogisticsObject = 'https://onerecord.iata.org/ns/api#hasLogisticsObject';

    /** The type of cargo:LogisticsObject in the notification e.g. https://onerecord.iata.org/ns/cargo#Piece */
    public const string hasLogisticsObjectType = 'https://onerecord.iata.org/ns/api#hasLogisticsObjectType';

    /** Message that describes the error */
    public const string hasMessage = 'https://onerecord.iata.org/ns/api#hasMessage';

    /** Operation(s) to apply as PATCH on a Logistics Object */
    public const string hasOperation = 'https://onerecord.iata.org/ns/api#hasOperation';

    public const string hasPermission = 'https://onerecord.iata.org/ns/api#hasPermission';

    /**
     * Property of the object for which the error applies in IRI format, i.e.
     * https://onerecord.iata.org/ns/cargo#hasVolumetricWeight
     */
    public const string hasProperty = 'https://onerecord.iata.org/ns/api#hasProperty';

    public const string hasRequestStatus = 'https://onerecord.iata.org/ns/api#hasRequestStatus';

    /**
     * Historical request status entries of an Action Request.
     *
     * @since 2.3.0
     */
    public const string hasRequestStatusHistory = 'https://onerecord.iata.org/ns/api#hasRequestStatusHistory';

    /**
     * Date and time from which the request status applies.
     *
     * @since 2.3.0
     */
    public const string hasRequestStatusSince = 'https://onerecord.iata.org/ns/api#hasRequestStatusSince';

    /** URI of the object where the error occurred */
    public const string hasResource = 'https://onerecord.iata.org/ns/api#hasResource';

    /**
     * Revision number of the Logistics Object, starting with 0 for changing the initial revision of a
     * Logistics Object
     */
    public const string hasRevision = 'https://onerecord.iata.org/ns/api#hasRevision';

    /** ONE Record API endpoint */
    public const string hasServerEndpoint = 'https://onerecord.iata.org/ns/api#hasServerEndpoint';

    /**
     * Contains the severity of the Error
     *
     * @since 2.3.0
     */
    public const string hasSeverity = 'https://onerecord.iata.org/ns/api#hasSeverity';

    public const string hasSubscriber = 'https://onerecord.iata.org/ns/api#hasSubscriber';

    /** Link to the requestors Subscription object with all subscription information */
    public const string hasSubscription = 'https://onerecord.iata.org/ns/api#hasSubscription';

    /** Supported ONE Record API versions by the server, MUST include at least one supported version. */
    public const string hasSupportedApiVersion = 'https://onerecord.iata.org/ns/api#hasSupportedApiVersion';

    /** Supported content types of the server, MUST contain at least application/ld+json */
    public const string hasSupportedContentType = 'https://onerecord.iata.org/ns/api#hasSupportedContentType';

    /** Optional list of supported encodings of the ONE Record server, e.g. gzip */
    public const string hasSupportedEncoding = 'https://onerecord.iata.org/ns/api#hasSupportedEncoding';

    /** Supported languages of the ONE Record API, minimum is en-US (American English) */
    public const string hasSupportedLanguage = 'https://onerecord.iata.org/ns/api#hasSupportedLanguage';

    /** Supported ontologies on the server, MUST be non-versioned IRIs */
    public const string hasSupportedOntology = 'https://onerecord.iata.org/ns/api#hasSupportedOntology';

    /** Supported ontology versions on the server, MUST be versioned IRIs */
    public const string hasSupportedOntologyVersion = 'https://onerecord.iata.org/ns/api#hasSupportedOntologyVersion';

    /** Short summary of the error */
    public const string hasTitle = 'https://onerecord.iata.org/ns/api#hasTitle';

    /**
     * The Logistics Object type or specific Logistics Object to which the subscription belongs to e.g.
     * https://onerecord.iata.org/Piece or https://1r.example.com/7f01363f-0c6a-4414-be48-d3692e219b91
     */
    public const string hasTopic = 'https://onerecord.iata.org/ns/api#hasTopic';

    public const string hasTopicType = 'https://onerecord.iata.org/ns/api#hasTopicType';

    /**
     * The number of total items created
     *
     * @since 2.3.0
     */
    public const string hasTotalCreated = 'https://onerecord.iata.org/ns/api#hasTotalCreated';

    /**
     * The total number of failed creations
     *
     * @since 2.3.0
     */
    public const string hasTotalFailed = 'https://onerecord.iata.org/ns/api#hasTotalFailed';

    /** The number of total items contained in a collection */
    public const string hasTotalItems = 'https://onerecord.iata.org/ns/api#hasTotalItems';

    /** Updated value for the field */
    public const string hasValue = 'https://onerecord.iata.org/ns/api#hasValue';

    /** Links to the Verification class */
    public const string hasVerification = 'https://onerecord.iata.org/ns/api#hasVerification';

    /** Link to the Verification Request */
    public const string hasVerificationRequest = 'https://onerecord.iata.org/ns/api#hasVerificationRequest';

    /**
     * An array used to indicate the specific types of notifications that the subscriber desires to receive
     * from the publisher. The subscriber is required to specify their preferences on a per-type basis
     */
    public const string includeSubscriptionEventType = 'https://onerecord.iata.org/ns/api#includeSubscriptionEventType';

    /**
     * Organization that changed the request status.
     *
     * @since 2.3.0
     */
    public const string isChangedBy = 'https://onerecord.iata.org/ns/api#isChangedBy';

    /** Datetime when the request was created */
    public const string isRequestedAt = 'https://onerecord.iata.org/ns/api#isRequestedAt';

    /** Organization Identifier that represents the Organization that has requested the action */
    public const string isRequestedBy = 'https://onerecord.iata.org/ns/api#isRequestedBy';

    public const string isRequestedFor = 'https://onerecord.iata.org/ns/api#isRequestedFor';

    /** The datetime when the action request was revoked. */
    public const string isRevokedAt = 'https://onerecord.iata.org/ns/api#isRevokedAt';

    public const string isRevokedBy = 'https://onerecord.iata.org/ns/api#isRevokedBy';

    /**
     * Optional URI to the ChangeRequest that triggered a Notification if the eventType is one of
     * CHANGE_REQUEST_ACCEPTED, CHANGE_REQUEST_REJECT, or CHANGE_REQUEST_FAILED
     */
    public const string isTriggeredBy = 'https://onerecord.iata.org/ns/api#isTriggeredBy';

    /**
     * Flag specifying if the requestor wants to receive Notification from the publisher when the status of
     * an action request changed, default=FALSE
     */
    public const string notifyRequestStatusChange = 'https://onerecord.iata.org/ns/api#notifyRequestStatusChange';

    public const string o = 'https://onerecord.iata.org/ns/api#o';

    public const string op = 'https://onerecord.iata.org/ns/api#op';

    /**
     * Operations objects must have exactly one p, predicate, member. The value of this member must be an
     * URI, e.g. https://onerecord.iata.org/ns/cargo#hasGoodsDescription
     */
    public const string p = 'https://onerecord.iata.org/ns/api#p';

    /**
     * Operation objects MUST have exactly one "s", subject, member. The value of this member MUST be one
     * of IRI or blank node.
     */
    public const string s = 'https://onerecord.iata.org/ns/api#s';

    /**
     * Flag specifying if the publisher should send the whole logistics object or not in the notification
     * object
     */
    public const string sendLogisticsObjectBody = 'https://onerecord.iata.org/ns/api#sendLogisticsObjectBody';

    /**
     * Indicates the orchestration version used to map the information with messaging standards
     *
     * @since 2.3.0
     */
    public const string usesOrchestration = 'https://onerecord.iata.org/ns/api#usesOrchestration';


    // Named individuals

    public const string ACCESS_DELEGATION_REQUEST_ACCEPTED = 'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_ACCEPTED';

    public const string ACCESS_DELEGATION_REQUEST_FAILED = 'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_FAILED';

    public const string ACCESS_DELEGATION_REQUEST_PENDING = 'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_PENDING';

    public const string ACCESS_DELEGATION_REQUEST_REJECTED = 'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_REJECTED';

    public const string ACCESS_DELEGATION_REQUEST_REVOKED = 'https://onerecord.iata.org/ns/api#ACCESS_DELEGATION_REQUEST_REVOKED';

    /** Defines a :PatchOperation to be an operation that adds new triples. */
    public const string ADD = 'https://onerecord.iata.org/ns/api#ADD';

    /** :EventType for accepted :ChangeRequests */
    public const string CHANGE_REQUEST_ACCEPTED = 'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_ACCEPTED';

    /** :EventType for failed :ChangeRequests. */
    public const string CHANGE_REQUEST_FAILED = 'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_FAILED';

    /** :EventType for pending :ChangeRequests. */
    public const string CHANGE_REQUEST_PENDING = 'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_PENDING';

    /** :EventType for rejected :ChangeRequests. */
    public const string CHANGE_REQUEST_REJECTED = 'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_REJECTED';

    public const string CHANGE_REQUEST_REVOKED = 'https://onerecord.iata.org/ns/api#CHANGE_REQUEST_REVOKED';

    public const string DELETE = 'https://onerecord.iata.org/ns/api#DELETE';

    /** @since 2.3.0 */
    public const string ERROR = 'https://onerecord.iata.org/ns/api#ERROR';

    /** :Permission to get a :LogisticsEvent */
    public const string GET_LOGISTICS_EVENT = 'https://onerecord.iata.org/ns/api#GET_LOGISTICS_EVENT';

    /** :Permission to get a :LogisticsObject */
    public const string GET_LOGISTICS_OBJECT = 'https://onerecord.iata.org/ns/api#GET_LOGISTICS_OBJECT';

    public const string LOGISTICS_EVENT_RECEIVED = 'https://onerecord.iata.org/ns/api#LOGISTICS_EVENT_RECEIVED';

    public const string LOGISTICS_OBJECT_ACCESS_GRANTED = 'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_ACCESS_GRANTED';

    public const string LOGISTICS_OBJECT_AVAILABLE = 'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_AVAILABLE';

    public const string LOGISTICS_OBJECT_CREATED = 'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_CREATED';

    public const string LOGISTICS_OBJECT_IDENTIFIER = 'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_IDENTIFIER';

    public const string LOGISTICS_OBJECT_TYPE = 'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_TYPE';

    public const string LOGISTICS_OBJECT_UPDATED = 'https://onerecord.iata.org/ns/api#LOGISTICS_OBJECT_UPDATED';

    public const string PATCH_LOGISTICS_OBJECT = 'https://onerecord.iata.org/ns/api#PATCH_LOGISTICS_OBJECT';

    /** :Permission to add a logistics event. */
    public const string POST_LOGISTICS_EVENT = 'https://onerecord.iata.org/ns/api#POST_LOGISTICS_EVENT';

    public const string REQUEST_ACCEPTED = 'https://onerecord.iata.org/ns/api#REQUEST_ACCEPTED';

    public const string REQUEST_ACKNOWLEDGED = 'https://onerecord.iata.org/ns/api#REQUEST_ACKNOWLEDGED';

    public const string REQUEST_FAILED = 'https://onerecord.iata.org/ns/api#REQUEST_FAILED';

    public const string REQUEST_PENDING = 'https://onerecord.iata.org/ns/api#REQUEST_PENDING';

    public const string REQUEST_REJECTED = 'https://onerecord.iata.org/ns/api#REQUEST_REJECTED';

    public const string REQUEST_REVOKED = 'https://onerecord.iata.org/ns/api#REQUEST_REVOKED';

    public const string SUBSCRIPTION_REQUEST_ACCEPTED = 'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_ACCEPTED';

    public const string SUBSCRIPTION_REQUEST_FAILED = 'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_FAILED';

    public const string SUBSCRIPTION_REQUEST_PENDING = 'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_PENDING';

    public const string SUBSCRIPTION_REQUEST_REJECTED = 'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_REJECTED';

    public const string SUBSCRIPTION_REQUEST_REVOKED = 'https://onerecord.iata.org/ns/api#SUBSCRIPTION_REQUEST_REVOKED';

    public const string VERIFICATION_REQUEST_ACKNOWLEDGED = 'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_ACKNOWLEDGED';

    public const string VERIFICATION_REQUEST_FAILED = 'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_FAILED';

    public const string VERIFICATION_REQUEST_PENDING = 'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_PENDING';

    public const string VERIFICATION_REQUEST_REJECTED = 'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_REJECTED';

    public const string VERIFICATION_REQUEST_REVOKED = 'https://onerecord.iata.org/ns/api#VERIFICATION_REQUEST_REVOKED';

    /** @since 2.3.0 */
    public const string WARNING = 'https://onerecord.iata.org/ns/api#WARNING';
}
