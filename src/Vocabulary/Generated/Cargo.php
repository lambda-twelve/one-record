<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated;

/** Term IRIs of the ONE Record cargo ontology (data model): classes, properties and named individuals. */
final class Cargo
{
    public const string NAMESPACE = 'https://onerecord.iata.org/ns/cargo#';

    // Classes

    /** Account type and number of a Party */
    public const string AccountNumber = 'https://onerecord.iata.org/ns/cargo#AccountNumber';

    /** Open code list for Account types */
    public const string AccountType = 'https://onerecord.iata.org/ns/cargo#AccountType';

    /** Embedded object used for AWB mapping (box 10) */
    public const string AccountingNote = 'https://onerecord.iata.org/ns/cargo#AccountingNote';

    /** Restricted code list for acceptable action times */
    public const string ActionTimeType = 'https://onerecord.iata.org/ns/cargo#ActionTimeType';

    /** Embedded object to create a sequence of Activities in the context of a Service */
    public const string ActivitySequence = 'https://onerecord.iata.org/ns/cargo#ActivitySequence';

    /** Superclass: Actors are Persons or entities acting like a single person */
    public const string Actor = 'https://onerecord.iata.org/ns/cargo#Actor';

    /** Address details */
    public const string Address = 'https://onerecord.iata.org/ns/cargo#Address';

    /** Adjustments in the context of CASS records */
    public const string Adjustments = 'https://onerecord.iata.org/ns/cargo#Adjustments';

    /** Answer holds the answer to one Question */
    public const string Answer = 'https://onerecord.iata.org/ns/cargo#Answer';

    /**
     * In the context of CASS2. process, BillingDetails object is used to integrate specific Billing and
     * Settlement data requirements
     */
    public const string BillingDetails = 'https://onerecord.iata.org/ns/cargo#BillingDetails';

    /** Booking object refers to a confirmed booking */
    public const string Booking = 'https://onerecord.iata.org/ns/cargo#Booking';

    /** Booking details */
    public const string BookingOption = 'https://onerecord.iata.org/ns/cargo#BookingOption';

    /** Request object, refers to the Quote request or Booking request */
    public const string BookingOptionRequest = 'https://onerecord.iata.org/ns/cargo#BookingOptionRequest';

    /** Restricted code list containing the statuses of a booking option */
    public const string BookingOptionStatus = 'https://onerecord.iata.org/ns/cargo#BookingOptionStatus';

    /** BookingPreferences details */
    public const string BookingPreferences = 'https://onerecord.iata.org/ns/cargo#BookingPreferences';

    /**
     * A party, usually the freight forwarder, creates the BookingRequest in order to confirm the booking
     * to the Carrier
     */
    public const string BookingRequest = 'https://onerecord.iata.org/ns/cargo#BookingRequest';

    /**
     * Information about the booking status on segment level, assigning pieces and ULDs to a TransportLeg
     * with a Space Allocation Code
     */
    public const string BookingSegment = 'https://onerecord.iata.org/ns/cargo#BookingSegment';

    /**
     * Simplified shipment object that is to be used only for the distribution scope where only a subset of
     * data is known priori to operational phase.
     */
    public const string BookingShipment = 'https://onerecord.iata.org/ns/cargo#BookingShipment';

    /** Restricted code list containing the possible statuses of a booking */
    public const string BookingStatus = 'https://onerecord.iata.org/ns/cargo#BookingStatus';

    /**
     * Previously called Schedule. This object refers to times used for the Booking Option Request
     * (preferences part of the request) or the Booking Option (times sur as LAT where there is a
     * commitment from the carrier)
     */
    public const string BookingTimes = 'https://onerecord.iata.org/ns/cargo#BookingTimes';

    /** CO2 Calculation */
    public const string CO2Emissions = 'https://onerecord.iata.org/ns/cargo#CO2Emissions';

    /** Company details of carriers */
    public const string Carrier = 'https://onerecord.iata.org/ns/cargo#Carrier';

    /** Carrier product details */
    public const string CarrierProduct = 'https://onerecord.iata.org/ns/cargo#CarrierProduct';

    /** Product additional details */
    public const string Characteristic = 'https://onerecord.iata.org/ns/cargo#Characteristic';

    /** Action to describe a check */
    public const string Check = 'https://onerecord.iata.org/ns/cargo#Check';

    /** Body of a Check referencing various Questions */
    public const string CheckTemplate = 'https://onerecord.iata.org/ns/cargo#CheckTemplate';

    /** Result of a Check */
    public const string CheckTotalResult = 'https://onerecord.iata.org/ns/cargo#CheckTotalResult';

    /**
     * Embedded object to transmit codes from non-RDF code lists in 1R in a semi-structured way. Code lists
     * may be externally maintained codes (such as HS codes) or carrier-specific codes. If a code is
     * present in RDF-form as Named Individual (like in the 1R core code lists ontology), it suffices to
     * put in its IRI
     */
    public const string CodeListElement = 'https://onerecord.iata.org/ns/cargo#CodeListElement';

    /** Company details */
    public const string Company = 'https://onerecord.iata.org/ns/cargo#Company';

    /** Action to describe build-up or break-down of LoadingUnits */
    public const string Composing = 'https://onerecord.iata.org/ns/cargo#Composing';

    /** Restricted code list for Composing subtypes */
    public const string CompositionType = 'https://onerecord.iata.org/ns/cargo#CompositionType';

    /** Contact details */
    public const string ContactDetail = 'https://onerecord.iata.org/ns/cargo#ContactDetail';

    /** Open code list for types of contact details */
    public const string ContactDetailType = 'https://onerecord.iata.org/ns/cargo#ContactDetailType';

    /** Open code list for roles of a contact */
    public const string ContactRole = 'https://onerecord.iata.org/ns/cargo#ContactRole';

    /** Embedded object to transmit currencies */
    public const string CurrencyValue = 'https://onerecord.iata.org/ns/cargo#CurrencyValue';

    /** Customs information details */
    public const string CustomsInformation = 'https://onerecord.iata.org/ns/cargo#CustomsInformation';

    /** Dangerous goods declaration */
    public const string DgDeclaration = 'https://onerecord.iata.org/ns/cargo#DgDeclaration';

    /** Details of the radioactive products */
    public const string DgProductRadioactive = 'https://onerecord.iata.org/ns/cargo#DgProductRadioactive';

    /** Details of the radioactive isotope contained in the product */
    public const string DgRadioactiveIsotope = 'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope';

    /** Dimension details */
    public const string Dimensions = 'https://onerecord.iata.org/ns/cargo#Dimensions';

    /** Restricted code list for the direction of a MovementTime */
    public const string DirectionType = 'https://onerecord.iata.org/ns/cargo#DirectionType';

    /**
     * Details of the pieces (Live animals) of the permit and specific information such as quantity
     * measured and used to date quota
     */
    public const string EpermitConsignment = 'https://onerecord.iata.org/ns/cargo#EpermitConsignment';

    /** Signature details of the Epermit for Live Animals */
    public const string EpermitSignature = 'https://onerecord.iata.org/ns/cargo#EpermitSignature';

    /** Restricted code list for acceptable event times */
    public const string EventTimeType = 'https://onerecord.iata.org/ns/cargo#EventTimeType';

    /** Restricted code list for the execution status of activities */
    public const string ExecutionStatus = 'https://onerecord.iata.org/ns/cargo#ExecutionStatus';

    /** Reference documents details */
    public const string ExternalReference = 'https://onerecord.iata.org/ns/cargo#ExternalReference';

    /** Geolocation details - e.g. for drones, automated vehicles */
    public const string Geolocation = 'https://onerecord.iata.org/ns/cargo#Geolocation';

    /** Generic handling service for non main carriage activities */
    public const string HandlingService = 'https://onerecord.iata.org/ns/cargo#HandlingService';

    /** Insurance details */
    public const string Insurance = 'https://onerecord.iata.org/ns/cargo#Insurance';

    /** IoT Device details */
    public const string IotDevice = 'https://onerecord.iata.org/ns/cargo#IotDevice';

    /** Item details */
    public const string Item = 'https://onerecord.iata.org/ns/cargo#Item';

    /** Dangerous Goods subtype of Item */
    public const string ItemDg = 'https://onerecord.iata.org/ns/cargo#ItemDg';

    /** Grouping of pieces for rating information as per Waybill box 22I */
    public const string LineItemPackage = 'https://onerecord.iata.org/ns/cargo#LineItemPackage';

    /** Epermit for Live Animals details */
    public const string LiveAnimalsEpermit = 'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit';

    /** Restricted code list for the Load Type of a piece or shipment */
    public const string LoadType = 'https://onerecord.iata.org/ns/cargo#LoadType';

    /** Action to describe onloading or offloading TransportMeans */
    public const string Loading = 'https://onerecord.iata.org/ns/cargo#Loading';

    /** LoadingMaterial describes transportable, complementary non-Piece objects such as dry ice or nets */
    public const string LoadingMaterial = 'https://onerecord.iata.org/ns/cargo#LoadingMaterial';

    /** Restricted code list for Loading subtypes */
    public const string LoadingType = 'https://onerecord.iata.org/ns/cargo#LoadingType';

    /** Common loading unit/container details */
    public const string LoadingUnit = 'https://onerecord.iata.org/ns/cargo#LoadingUnit';

    /** Location describes a physical location, e.g. an airport, a warehouse or a truck deck */
    public const string Location = 'https://onerecord.iata.org/ns/cargo#Location';

    /**
     * Superclass: LogisticsAction is a specific task with a specific result performed on one or more
     * physical LOs by one party in the context of an Activity
     */
    public const string LogisticsAction = 'https://onerecord.iata.org/ns/cargo#LogisticsAction';

    /**
     * Superclass: LogisticsActivity is a scheduled set of tasks that is executed as part of one or more
     * Services
     */
    public const string LogisticsActivity = 'https://onerecord.iata.org/ns/cargo#LogisticsActivity';

    /**
     * Superclass: LogisticsAgents describe acting entities in the logistics supply chain such as persons
     * and organizations
     */
    public const string LogisticsAgent = 'https://onerecord.iata.org/ns/cargo#LogisticsAgent';

    /** Event details */
    public const string LogisticsEvent = 'https://onerecord.iata.org/ns/cargo#LogisticsEvent';

    /** Logistics Object parent class, containing all common properties for logistics objects. */
    public const string LogisticsObject = 'https://onerecord.iata.org/ns/cargo#LogisticsObject';

    /** Superclass: LogisticsService is a sequence of Activities provided by one Party to another */
    public const string LogisticsService = 'https://onerecord.iata.org/ns/cargo#LogisticsService';

    /** LoosePiece details */
    public const string LoosePiece = 'https://onerecord.iata.org/ns/cargo#LoosePiece';

    /** Measurements details for Sensors, either generic or geolocation measurements are recorded */
    public const string Measurement = 'https://onerecord.iata.org/ns/cargo#Measurement';

    /** Open code list for transport modes */
    public const string ModeQualifier = 'https://onerecord.iata.org/ns/cargo#ModeQualifier';

    /**
     * Times referring to Transport Movements, used to describe specfic times such as Actual Departure
     * time, etc.
     */
    public const string MovementTime = 'https://onerecord.iata.org/ns/cargo#MovementTime';

    /** Restricted code list for MovementTime subtypes */
    public const string MovementTimeType = 'https://onerecord.iata.org/ns/cargo#MovementTimeType';

    /** Non-human actors are actors which are not a person, such as robots */
    public const string NonHumanActor = 'https://onerecord.iata.org/ns/cargo#NonHumanActor';

    /**
     * Superclass: Organizations represent a kind of Agent corresponding to social institutions such as
     * companies, societies, etc
     */
    public const string Organization = 'https://onerecord.iata.org/ns/cargo#Organization';

    /** Other Charge details from AWB as per bullet point 19 - data element 23 from AWB */
    public const string OtherCharge = 'https://onerecord.iata.org/ns/cargo#OtherCharge';

    /** Other identifiers */
    public const string OtherIdentifier = 'https://onerecord.iata.org/ns/cargo#OtherIdentifier';

    /** Packaging details */
    public const string PackagingType = 'https://onerecord.iata.org/ns/cargo#PackagingType';

    /**
     * Refers to a Company and its role in a specific context, e.g Company A as shipper. Cargo-XML Code
     * List 1.15 can be used as a reference with the addition of "Notify Party"
     */
    public const string Party = 'https://onerecord.iata.org/ns/cargo#Party';

    /** Person details */
    public const string Person = 'https://onerecord.iata.org/ns/cargo#Person';

    /**
     * Superclass: PhysicalLogisticObjects represent the digital twin of an object in the logistics supply
     * chain that physically exist
     */
    public const string PhysicalLogisticsObject = 'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject';

    /** Individual piece or virtual grouping of pieces */
    public const string Piece = 'https://onerecord.iata.org/ns/cargo#Piece';

    /** Dangerous Goods subtype of Piece */
    public const string PieceDg = 'https://onerecord.iata.org/ns/cargo#PieceDg';

    /** PieceGroup details */
    public const string PieceGroup = 'https://onerecord.iata.org/ns/cargo#PieceGroup';

    /** LiveAnimals subclass of Piece */
    public const string PieceLiveAnimals = 'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals';

    /** Price associated to the offer/booking */
    public const string Price = 'https://onerecord.iata.org/ns/cargo#Price';

    /** Product details */
    public const string Product = 'https://onerecord.iata.org/ns/cargo#Product';

    /** Dangerous Goods subtype of Product */
    public const string ProductDg = 'https://onerecord.iata.org/ns/cargo#ProductDg';

    /** PublicAuthorities are Organizations of the state on public interests, such as customs */
    public const string PublicAuthority = 'https://onerecord.iata.org/ns/cargo#PublicAuthority';

    /** Question as part of a CheckTemplate */
    public const string Question = 'https://onerecord.iata.org/ns/cargo#Question';

    /** Ranges details */
    public const string Ranges = 'https://onerecord.iata.org/ns/cargo#Ranges';

    /** Ratings details */
    public const string Ratings = 'https://onerecord.iata.org/ns/cargo#Ratings';

    /** Regulated Entity */
    public const string RegulatedEntity = 'https://onerecord.iata.org/ns/cargo#RegulatedEntity';

    /** Security declaration details */
    public const string SecurityDeclaration = 'https://onerecord.iata.org/ns/cargo#SecurityDeclaration';

    /** Sensor details and measurements, linked to Connected Devices */
    public const string Sensor = 'https://onerecord.iata.org/ns/cargo#Sensor';

    /** Open code list for sensor types */
    public const string SensorType = 'https://onerecord.iata.org/ns/cargo#SensorType';

    /** Shipment details */
    public const string Shipment = 'https://onerecord.iata.org/ns/cargo#Shipment';

    /** StationRemarks details */
    public const string StationRemarks = 'https://onerecord.iata.org/ns/cargo#StationRemarks';

    /**
     * Dedicated event for transmitting status update information on shipment level for transition period
     *
     * @since 3.3
     */
    public const string StatusUpdateEvent = 'https://onerecord.iata.org/ns/cargo#StatusUpdateEvent';

    /** Activity to describe storing processes */
    public const string Storage = 'https://onerecord.iata.org/ns/cargo#Storage';

    /** Action to describe store-in or store-out */
    public const string Storing = 'https://onerecord.iata.org/ns/cargo#Storing';

    /** Restricted code list for Storing subtypes */
    public const string StoringType = 'https://onerecord.iata.org/ns/cargo#StoringType';

    /** TemperatureInstructions details */
    public const string TemperatureInstructions = 'https://onerecord.iata.org/ns/cargo#TemperatureInstructions';

    /** TransportLegs details */
    public const string TransportLegs = 'https://onerecord.iata.org/ns/cargo#TransportLegs';

    /** Transport means details */
    public const string TransportMeans = 'https://onerecord.iata.org/ns/cargo#TransportMeans';

    /** Activity to describe transports, replaces the TransportSegment in v1.1 and above */
    public const string TransportMovement = 'https://onerecord.iata.org/ns/cargo#TransportMovement';

    /** Unit Load Device details */
    public const string ULD = 'https://onerecord.iata.org/ns/cargo#ULD';

    /** ULDBasicPiece details */
    public const string ULDBasicPiece = 'https://onerecord.iata.org/ns/cargo#ULDBasicPiece';

    /** ULDSpecificPiece details */
    public const string ULDSpecificPiece = 'https://onerecord.iata.org/ns/cargo#ULDSpecificPiece';

    /** Activity to describe composition and decomposition of LoadingUnits */
    public const string UnitComposition = 'https://onerecord.iata.org/ns/cargo#UnitComposition';

    /** UnitsPreference details */
    public const string UnitsPreference = 'https://onerecord.iata.org/ns/cargo#UnitsPreference';

    /** Unit of measurement details */
    public const string Value = 'https://onerecord.iata.org/ns/cargo#Value';

    /** VolumePieceGroup details */
    public const string VolumePieceGroup = 'https://onerecord.iata.org/ns/cargo#VolumePieceGroup';

    /**
     * Volumetric weight details
     *
     * @deprecated since 3.2
     */
    public const string VolumetricWeight = 'https://onerecord.iata.org/ns/cargo#VolumetricWeight';

    /** Waybill details */
    public const string Waybill = 'https://onerecord.iata.org/ns/cargo#Waybill';

    /**
     * Information from AWB Rate Description section as per bullet point 18 - data elements 22A - 22Z from
     * AWB. Data describing Piece and Package parameters to be transmitted per Piece object using
     * pieceReferences; Data describing ULD parameters to be transmitted per ULD object using uldReference.
     */
    public const string WaybillLineItem = 'https://onerecord.iata.org/ns/cargo#WaybillLineItem';

    /** Restricted code list for Waybill types */
    public const string WaybillType = 'https://onerecord.iata.org/ns/cargo#WaybillType';


    // Properties

    /** Type of the account of the account number */
    public const string accountNumberType = 'https://onerecord.iata.org/ns/cargo#accountNumberType';

    /** Information about account numbers */
    public const string accountNumbers = 'https://onerecord.iata.org/ns/cargo#accountNumbers';

    /**
     * Indicates the details of accounting information. Free text e.g. PAYMENT BY CERTIFIED CHEQUE etc.
     *
     * @deprecated since 3.2
     */
    public const string accountingInformation = 'https://onerecord.iata.org/ns/cargo#accountingInformation';

    /** String holding accounting information (AWB box 10) */
    public const string accountingNoteIdentifier = 'https://onerecord.iata.org/ns/cargo#accountingNoteIdentifier';

    /** String holding the identifier in an accounting note (AWB box 10) */
    public const string accountingNoteText = 'https://onerecord.iata.org/ns/cargo#accountingNoteText';

    /** Information about accounting notes (AWB box 10) */
    public const string accountingNotes = 'https://onerecord.iata.org/ns/cargo#accountingNotes';

    /** Defined in Resolution Conf. 13.6 and is required for pre-Convention specimens (box 12b) */
    public const string acquisitionDateTime = 'https://onerecord.iata.org/ns/cargo#acquisitionDateTime';

    /** DateTime holding the end time of the Action; Type is indicated through ActionType property */
    public const string actionEndTime = 'https://onerecord.iata.org/ns/cargo#actionEndTime';

    /** DateTime holding the start time of the Action; Type is indicated through ActionType property */
    public const string actionStartTime = 'https://onerecord.iata.org/ns/cargo#actionStartTime';

    /** Enum stating the type of the Action */
    public const string actionTimeType = 'https://onerecord.iata.org/ns/cargo#actionTimeType';

    /** Reference to the Activity that is performed as part of a Service */
    public const string activity = 'https://onerecord.iata.org/ns/cargo#activity';

    /** Numeric expression of the activity of a radioactive Item */
    public const string activityLevelMeasure = 'https://onerecord.iata.org/ns/cargo#activityLevelMeasure';

    /** Information about the Activities that are part of the Service and their sequence */
    public const string activitySequences = 'https://onerecord.iata.org/ns/cargo#activitySequences';

    /**
     * Identifies the subsidiary hazard class / division identification containing a numeric field
     * separated by a decimal. There may be , 1 or 2 subsidiary risk classes or divisions. If there is more
     * than one, each should be separated by a comma. The subsidiary risk must be shown in parentheses.
     */
    public const string additionalHazardClassificationId = 'https://onerecord.iata.org/ns/cargo#additionalHazardClassificationId';

    /** Additional information related to the Booking Option, e.g. sales details */
    public const string additionalInformation = 'https://onerecord.iata.org/ns/cargo#additionalInformation';

    /** Any additional information that may be required by an ICAO Member State */
    public const string additionalSecurityInformation = 'https://onerecord.iata.org/ns/cargo#additionalSecurityInformation';

    /** Address details */
    public const string address = 'https://onerecord.iata.org/ns/cargo#address';

    /** Address identifier using special coding systems e.g. US CBP FIRMS code */
    public const string addressCode = 'https://onerecord.iata.org/ns/cargo#addressCode';

    /** Information about Adjustments performed on the BillingDetails */
    public const string adjustments = 'https://onerecord.iata.org/ns/cargo#adjustments';

    /**
     * String holding the agent or freight forwarder reference of an AWB
     *
     * @since 3.3
     */
    public const string agentReference = 'https://onerecord.iata.org/ns/cargo#agentReference';

    /**
     * Contains the Special Handling Code related to the prescribed limitation. Hardcoded to PASSENGER AND
     * CARGO AIRCRAFT or CARGO AIRCRAFT ONLY. This field is mandatory for air (Air)
     */
    public const string aircraftLimitationInformation = 'https://onerecord.iata.org/ns/cargo#aircraftLimitationInformation';

    /** Type of aircraft to be used if any specific requirements (e.g. Pure freighter, etc.) */
    public const string aircraftPossibilityCode = 'https://onerecord.iata.org/ns/cargo#aircraftPossibilityCode';

    /** IATA two-character airline code */
    public const string airlineCode = 'https://onerecord.iata.org/ns/cargo#airlineCode';

    /**
     * A statement identifying that the dangerous goods listed above are all contained in the same outer
     * packaging. Takes the form All packed in one aaaa (description of packaging type) x nn (number of
     * packages). Applies to air transport only. (Air)
     */
    public const string allPackedInOneIndicator = 'https://onerecord.iata.org/ns/cargo#allPackedInOneIndicator';

    /** String holding an allotment code of a booking segment */
    public const string allotmentCode = 'https://onerecord.iata.org/ns/cargo#allotmentCode';

    /** Description of the alternatives proposed that do not match the Booking Option Request */
    public const string alternatives = 'https://onerecord.iata.org/ns/cargo#alternatives';

    /**
     * total number of specimens exported in the current calendar year and the current annual quota for the
     * species concerned (box 11a)
     */
    public const string annualQuotaQuantity = 'https://onerecord.iata.org/ns/cargo#annualQuotaQuantity';

    /** Reference to the Answer to the Question */
    public const string answer = 'https://onerecord.iata.org/ns/cargo#answer';

    /** Reference to the Actor giving the Answer */
    public const string answerActor = 'https://onerecord.iata.org/ns/cargo#answerActor';

    /** Text restrictions to the Answer */
    public const string answerOptionsText = 'https://onerecord.iata.org/ns/cargo#answerOptionsText';

    /** Value restrictions to the answer */
    public const string answerOptionsValue = 'https://onerecord.iata.org/ns/cargo#answerOptionsValue';

    /** Information about an answer Value of any kind of the Answer */
    public const string answerValue = 'https://onerecord.iata.org/ns/cargo#answerValue';

    /** Piece on which the Packaging type is applicable */
    public const string appliedOnPieces = 'https://onerecord.iata.org/ns/cargo#appliedOnPieces';

    /** Arrival date and time of the leg */
    public const string arrivalDate = 'https://onerecord.iata.org/ns/cargo#arrivalDate';

    /** Reference to the arrival Location */
    public const string arrivalLocation = 'https://onerecord.iata.org/ns/cargo#arrivalLocation';

    /** Reference to the permits associated with the Live Animals */
    public const string associatedEpermit = 'https://onerecord.iata.org/ns/cargo#associatedEpermit';

    /** Reference to the Organization the Actor is associated with */
    public const string associatedOrganization = 'https://onerecord.iata.org/ns/cargo#associatedOrganization';

    /** US / ATA Unit Load Device type code e.g. M2 */
    public const string ataDesignator = 'https://onerecord.iata.org/ns/cargo#ataDesignator';

    /** References to all connected IotDevices */
    public const string attachedIotDevices = 'https://onerecord.iata.org/ns/cargo#attachedIotDevices';

    /** Reference to the PhysicalLogisticsObject the IotDevice is attached to */
    public const string attachedToObject = 'https://onerecord.iata.org/ns/cargo#attachedToObject';

    /**
     * Contains additional information relating to an approval, permission or other specific detail
     * applicable to the commodity (e.g. Dangerous Goods in excepted quantities)
     */
    public const string authorizationInformation = 'https://onerecord.iata.org/ns/cargo#authorizationInformation';

    /** The Date AWB Acceptance should be the same as the Date AWB Delivery. (beginning of the process) */
    public const string awbAcceptanceDate = 'https://onerecord.iata.org/ns/cargo#awbAcceptanceDate';

    /**
     * The Date AWB Delivery is also used as the AWB Execution date which will determine which billing
     * period it will be processed and billed in.
     */
    public const string awbDeliveryDate = 'https://onerecord.iata.org/ns/cargo#awbDeliveryDate';

    /** The AWB execution date determines which billing period the document will be processed and billed in. */
    public const string awbExecutionDate = 'https://onerecord.iata.org/ns/cargo#awbExecutionDate';

    /** It must either contain the values of R for Revenue AWB, V for Void AWB or S for Service AWB. */
    public const string awbUseIndicator = 'https://onerecord.iata.org/ns/cargo#awbUseIndicator';

    /** Reference to the Location where the Organization is based at or headquartered */
    public const string basedAtLocation = 'https://onerecord.iata.org/ns/cargo#basedAtLocation';

    /** Production batch number / reference */
    public const string batchNumber = 'https://onerecord.iata.org/ns/cargo#batchNumber';

    /** Billing charge identifiers to be used for CASS. Refer to CargoXML Code List 1.33 */
    public const string billingChargeIdentifier = 'https://onerecord.iata.org/ns/cargo#billingChargeIdentifier';

    /** Reference to the BillingDetails of the Waybill */
    public const string billingDetails = 'https://onerecord.iata.org/ns/cargo#billingDetails';

    /** Reference to the Booking */
    public const string booking = 'https://onerecord.iata.org/ns/cargo#booking';

    /** Reference to all Booking Options */
    public const string bookingOptions = 'https://onerecord.iata.org/ns/cargo#bookingOptions';

    /** Reference to the Booking preferences */
    public const string bookingPreference = 'https://onerecord.iata.org/ns/cargo#bookingPreference';

    /** Reference to the Booking Request */
    public const string bookingRequest = 'https://onerecord.iata.org/ns/cargo#bookingRequest';

    /** Information about booking segments - physics allocated to a specific transport leg */
    public const string bookingSegments = 'https://onerecord.iata.org/ns/cargo#bookingSegments';

    /** Reference to the BookingShipment if required */
    public const string bookingShipmentDetails = 'https://onerecord.iata.org/ns/cargo#bookingShipmentDetails';

    /** Status of the Booking */
    public const string bookingStatus = 'https://onerecord.iata.org/ns/cargo#bookingStatus';

    /** Information about the Booking Times of a provided Booking Option */
    public const string bookingTimes = 'https://onerecord.iata.org/ns/cargo#bookingTimes';

    /** Reference to the Booking to update */
    public const string bookingToUpdate = 'https://onerecord.iata.org/ns/cargo#bookingToUpdate';

    /** CO2 emissions calculated */
    public const string calculatedEmissions = 'https://onerecord.iata.org/ns/cargo#calculatedEmissions';

    /** Reference to the TransportMovement or TransportLegs the CO2Emissions have been calculated for */
    public const string calculationFor = 'https://onerecord.iata.org/ns/cargo#calculationFor';

    /** Reference to the operating carrier */
    public const string carrier = 'https://onerecord.iata.org/ns/cargo#carrier';

    /** One letter charge code as per bullet point 12 - data element 13 from AWB */
    public const string carrierChargeCode = 'https://onerecord.iata.org/ns/cargo#carrierChargeCode';

    /** Date upon which the certification is made by the carrier */
    public const string carrierDeclarationDate = 'https://onerecord.iata.org/ns/cargo#carrierDeclarationDate';

    /**
     * Location of individual or company involved in the movement of a consignment or Coded representation
     * of a specific airport/city code
     */
    public const string carrierDeclarationPlace = 'https://onerecord.iata.org/ns/cargo#carrierDeclarationPlace';

    /** Contains the authentication of the Carrier */
    public const string carrierDeclarationSignature = 'https://onerecord.iata.org/ns/cargo#carrierDeclarationSignature';

    /** Reference to the Carrier product if known */
    public const string carrierProduct = 'https://onerecord.iata.org/ns/cargo#carrierProduct';

    /**
     * Operations code ID. Refers to the number of the registered captive-breeding or artificial
     * propagation operation (box 12b)
     */
    public const string categoryCode = 'https://onerecord.iata.org/ns/cargo#categoryCode';

    /** Reference to the Actor certifying the result of the Check if required */
    public const string certifiedByActor = 'https://onerecord.iata.org/ns/cargo#certifiedByActor';

    /** Product characteristics code - e.g. CLR - Color. Not restricted to a list. */
    public const string characteristicType = 'https://onerecord.iata.org/ns/cargo#characteristicType';

    /** Charge code, refer to CargoXML Code List 1.1 */
    public const string chargeCode = 'https://onerecord.iata.org/ns/cargo#chargeCode';

    /** Description of the charge e.g. Airfreight, fuel, etc. */
    public const string chargeDescription = 'https://onerecord.iata.org/ns/cargo#chargeDescription';

    /** Indicates if charge is prepaid or collect (P, C) */
    public const string chargePaymentType = 'https://onerecord.iata.org/ns/cargo#chargePaymentType';

    /** Double describing the time or item basis quantity of a charge */
    public const string chargeQuantity = 'https://onerecord.iata.org/ns/cargo#chargeQuantity';

    /** Charge type related to amount total as per bullet points 2/21 - data elements 24A - 3B from AWB */
    public const string chargeType = 'https://onerecord.iata.org/ns/cargo#chargeType';

    /** Chargeable weight */
    public const string chargeableWeight = 'https://onerecord.iata.org/ns/cargo#chargeableWeight';

    /**
     * Chargeable weight for which the rate description details apply
     *
     * @deprecated since 3.2
     */
    public const string chargeableWeightForRate = 'https://onerecord.iata.org/ns/cargo#chargeableWeightForRate';

    /** References to CheckActions performed for the Activity */
    public const string checkActions = 'https://onerecord.iata.org/ns/cargo#checkActions';

    /** Free text remarks to the check result */
    public const string checkRemark = 'https://onerecord.iata.org/ns/cargo#checkRemark';

    /** Reference to the CheckTemplate the Question is from */
    public const string checkTemplate = 'https://onerecord.iata.org/ns/cargo#checkTemplate';

    /** Reference to the result of the Check */
    public const string checkTotalResult = 'https://onerecord.iata.org/ns/cargo#checkTotalResult';

    /** Reference to the checked Object */
    public const string checkedObject = 'https://onerecord.iata.org/ns/cargo#checkedObject';

    /** Reference to the Actor performing the Check */
    public const string checker = 'https://onerecord.iata.org/ns/cargo#checker';

    /** References to the CheckActions performed on the object */
    public const string checks = 'https://onerecord.iata.org/ns/cargo#checks';

    /** Checksum of the document to validate its integrity */
    public const string checksum = 'https://onerecord.iata.org/ns/cargo#checksum';

    /** UN/LOCODE city code (5 letter) or IATA city code (3 letter) */
    public const string cityCode = 'https://onerecord.iata.org/ns/cargo#cityCode';

    /** String holding a city name */
    public const string cityName = 'https://onerecord.iata.org/ns/cargo#cityName';

    /** References to CO2Emissions */
    public const string co2Emissions = 'https://onerecord.iata.org/ns/cargo#co2Emissions';

    /**
     * Code or short version of a code, for example "CH" for Switzerland when referring to the UN/LOCODE
     * code list
     */
    public const string code = 'https://onerecord.iata.org/ns/cargo#code';

    /**
     * Description or long version of the code, for example "Switzerland" for Switzerland when referring to
     * the UN/LOCODE code list
     */
    public const string codeDescription = 'https://onerecord.iata.org/ns/cargo#codeDescription';

    /** Integer indicating the level of a code if a codelists is hierarchical, for example HS-Codes */
    public const string codeLevel = 'https://onerecord.iata.org/ns/cargo#codeLevel';

    /**
     * Official name of the code list without version number when direct reference is not possible, for
     * example "UN/LOCODE" when referring to the UN/LOCODE code list
     */
    public const string codeListName = 'https://onerecord.iata.org/ns/cargo#codeListName';

    /**
     * URL to access the code list the code is taken from, for example
     * "https://unece.org/trade/cefact/unlocode-code-list-country-and-territory" for UN/LOCODE.
     */
    public const string codeListReference = 'https://onerecord.iata.org/ns/cargo#codeListReference';

    /**
     * Version of the code list, for example "223-1" for UN/LOCODE. Used if the property codeListName is
     * used or the version is not apparent from the resource referred to in property codeListReference.
     */
    public const string codeListVersion = 'https://onerecord.iata.org/ns/cargo#codeListVersion';

    /** Coload indicator for the pieces (boolean) */
    public const string coload = 'https://onerecord.iata.org/ns/cargo#coload';

    /** The commission amount in favour of the Cargo Agent/Associate, applicable for the shipment concerned */
    public const string commission = 'https://onerecord.iata.org/ns/cargo#commission';

    /** Indicates if commission is applied. Boolean */
    public const string commissionIndicator = 'https://onerecord.iata.org/ns/cargo#commissionIndicator';

    /**
     * The commission percentage in favour of the Cargo Agent/Associate, applicable for the shipment
     * concerned
     */
    public const string commissionPercentage = 'https://onerecord.iata.org/ns/cargo#commissionPercentage';

    /** Indicates the specific commodity on which the rate class code is applied */
    public const string commodityItemNumber = 'https://onerecord.iata.org/ns/cargo#commodityItemNumber';

    /**
     * Indicates the specific commodity on which the rate class code is applied
     *
     * @deprecated since 3.2
     */
    public const string commodityItemNumberForRate = 'https://onerecord.iata.org/ns/cargo#commodityItemNumberForRate';

    /**
     * Contains the warning message complying with the regulations text note. This field is mandatory for
     * air (Air)
     */
    public const string complianceDeclarationText = 'https://onerecord.iata.org/ns/cargo#complianceDeclarationText';

    /** References to the Materials being built-up or broken-down */
    public const string composedMaterials = 'https://onerecord.iata.org/ns/cargo#composedMaterials';

    /** References to the Pieces being built-up or broken-down */
    public const string composedPieces = 'https://onerecord.iata.org/ns/cargo#composedPieces';

    /** References to all CompositionActions performed for the UnitComposition */
    public const string compositionActions = 'https://onerecord.iata.org/ns/cargo#compositionActions';

    /** Short text holding the process number if necessary */
    public const string compositionIdentifier = 'https://onerecord.iata.org/ns/cargo#compositionIdentifier';

    /** Enum stating whether the CompositionAction describes build-up or break-down */
    public const string compositionType = 'https://onerecord.iata.org/ns/cargo#compositionType';

    /** Reference to the sensors linked to the device */
    public const string connectedSensors = 'https://onerecord.iata.org/ns/cargo#connectedSensors';

    /**
     * Reference to the Organization that fulfills the role of the consignee, for a LiveAnimalsEpermit it
     * has to include complete name and address (box 3)
     */
    public const string consignee = 'https://onerecord.iata.org/ns/cargo#consignee';

    /** Reference to te pieces (Live Animals) of the permit */
    public const string consignmentItems = 'https://onerecord.iata.org/ns/cargo#consignmentItems';

    /** Reference to the pieces and properties linked to the Permit (box 7 to 12) */
    public const string consignments = 'https://onerecord.iata.org/ns/cargo#consignments';

    /** Name of consignor signatory */
    public const string consignorDeclarationSignature = 'https://onerecord.iata.org/ns/cargo#consignorDeclarationSignature';

    /** Indication if the shipment is a consolidation */
    public const string consolidationIndicator = 'https://onerecord.iata.org/ns/cargo#consolidationIndicator';

    /** Type of the contact details, e.g. Phone number, Mail address */
    public const string contactDetailType = 'https://onerecord.iata.org/ns/cargo#contactDetailType';

    /** Information about contactDetails */
    public const string contactDetails = 'https://onerecord.iata.org/ns/cargo#contactDetails';

    /** References to Actors (Person, NonHumanActor) acting as contacts */
    public const string contactPersons = 'https://onerecord.iata.org/ns/cargo#contactPersons';

    /** Contact type - e.g. Emergency contact, Customs contact, Customer contact */
    public const string contactRole = 'https://onerecord.iata.org/ns/cargo#contactRole';

    /** Reference to the item(s) contained in the piece */
    public const string containedItems = 'https://onerecord.iata.org/ns/cargo#containedItems';

    /** Details of contained piece(s) */
    public const string containedPieces = 'https://onerecord.iata.org/ns/cargo#containedPieces';

    /**
     * Customs, Security and Regulatory Control Information Identifier. Coded indicator qualifying Customs
     * related information: Item Number "I", Exemption Legend "L", System Downtime Reference "S", Unique
     * Consignment Reference Number "U", Movement Reference Number "M" . Refers to Code List 1.1.
     * Condition: At least one of the three elements (Country Code, Information Identifier or Customs,
     * Security and Regulatory Control Information Identifier) must be completed
     */
    public const string contentCode = 'https://onerecord.iata.org/ns/cargo#contentCode';

    /** Reference to the DgProductRadioactive this Isotope is contained in */
    public const string contentOfDgProductRadioactive = 'https://onerecord.iata.org/ns/cargo#contentOfDgProductRadioactive';

    /** Goods production country, mandatory when there are no Items. Refer ISO 3166-2 */
    public const string contentProductionCountry = 'https://onerecord.iata.org/ns/cargo#contentProductionCountry';

    /**
     * Reference to the Products describing the content of the Piece, mandatory if no data on Item level is
     * used
     */
    public const string contentProducts = 'https://onerecord.iata.org/ns/cargo#contentProducts';

    /** Volume to weight conversion factor */
    public const string conversionFactor = 'https://onerecord.iata.org/ns/cargo#conversionFactor';

    /** Indicates if the permit is a copy (true) or an original (false) (box 1) */
    public const string copyIndicator = 'https://onerecord.iata.org/ns/cargo#copyIndicator';

    /** Number of the adjustment */
    public const string correctionNumber = 'https://onerecord.iata.org/ns/cargo#correctionNumber';

    /** Serial Number of the correction */
    public const string correctionSerialNumber = 'https://onerecord.iata.org/ns/cargo#correctionSerialNumber';

    /** Country details. Refer ISO 3166-2 */
    public const string country = 'https://onerecord.iata.org/ns/cargo#country';

    /** Party covering the insurance */
    public const string coveringOrganization = 'https://onerecord.iata.org/ns/cargo#coveringOrganization';

    /** Location of the document, e.g. location where the document was emitted */
    public const string createdAtLocation = 'https://onerecord.iata.org/ns/cargo#createdAtLocation';

    /** DateTime at which the LogisticsEvent was posted */
    public const string creationDate = 'https://onerecord.iata.org/ns/cargo#creationDate';

    /**
     * Applies to fissile material only, other than fissile excepted. A numeric value expressed to one
     * decimal place preceded by the letters CSI.
     */
    public const string criticalitySafetyIndexNumeric = 'https://onerecord.iata.org/ns/cargo#criticalitySafetyIndexNumeric';

    /** Preferred unit for currency */
    public const string currency = 'https://onerecord.iata.org/ns/cargo#currency';

    /**
     * Information about the currency used in a CurrencyValue. Create an instance of CurrencyCode based on
     * ISO 4217
     */
    public const string currencyUnit = 'https://onerecord.iata.org/ns/cargo#currencyUnit';

    /** Customs details */
    public const string customsInformation = 'https://onerecord.iata.org/ns/cargo#customsInformation';

    /**
     * Code indicating the origin of goods for Customs purposes (e.g. For goods in free circulation in the
     * EU) List to be provided by local authorities
     */
    public const string customsOriginCode = 'https://onerecord.iata.org/ns/cargo#customsOriginCode';

    /** Indicates if the ULD is Damaged */
    public const string damageFlag = 'https://onerecord.iata.org/ns/cargo#damageFlag';

    /** DateTime on which the CheckTemplate was released */
    public const string date = 'https://onerecord.iata.org/ns/cargo#date';

    /** Date and time at which the DgDeclaration was declared */
    public const string declarationDate = 'https://onerecord.iata.org/ns/cargo#declarationDate';

    /** Reference to the Location the DgDeclaration was declared at */
    public const string declarationPlace = 'https://onerecord.iata.org/ns/cargo#declarationPlace';

    /** The value of a shipment declared for carriage purposes */
    public const string declaredValueForCarriage = 'https://onerecord.iata.org/ns/cargo#declaredValueForCarriage';

    /** The value of a shipment declared for customs purposes */
    public const string declaredValueForCustoms = 'https://onerecord.iata.org/ns/cargo#declaredValueForCustoms';

    /** Contains three designator of demurrage code, refer to RP 1654 (BCC, HHH, XXX, ZZZ) */
    public const string demurrageCode = 'https://onerecord.iata.org/ns/cargo#demurrageCode';

    /** Density Group Code as defined in cXML code list 2 */
    public const string densityGroupCode = 'https://onerecord.iata.org/ns/cargo#densityGroupCode';

    /** Department / Division / Unit */
    public const string department = 'https://onerecord.iata.org/ns/cargo#department';

    /** Departure date and time of the leg */
    public const string departureDate = 'https://onerecord.iata.org/ns/cargo#departureDate';

    /** Reference to the departure Location */
    public const string departureLocation = 'https://onerecord.iata.org/ns/cargo#departureLocation';

    /** Reference to the Items or Pieces in which the product can be found. */
    public const string describedObjects = 'https://onerecord.iata.org/ns/cargo#describedObjects';

    /** Natural language description if required */
    public const string description = 'https://onerecord.iata.org/ns/cargo#description';

    /** Charges levied at destination accruing to the last carrier, in destination currency */
    public const string destinationCharges = 'https://onerecord.iata.org/ns/cargo#destinationCharges';

    /** Conversion rate applied */
    public const string destinationCurrencyRate = 'https://onerecord.iata.org/ns/cargo#destinationCurrencyRate';

    /** Reference to the Waybill */
    public const string detailedWaybill = 'https://onerecord.iata.org/ns/cargo#detailedWaybill';

    /** Commercial denomination of the device */
    public const string deviceModel = 'https://onerecord.iata.org/ns/cargo#deviceModel';

    /** Reference to the Dangerous Goods declaration */
    public const string dgDeclaration = 'https://onerecord.iata.org/ns/cargo#dgDeclaration';

    /**
     * The category of the package or all packed in one. Complete text to be transmitted: I-White,
     * II-Yellow, III-Yellow instead of I, II, III
     */
    public const string dgRaTypeCode = 'https://onerecord.iata.org/ns/cargo#dgRaTypeCode';

    /** Dg Radioactive Material */
    public const string dgRadioactiveMaterial = 'https://onerecord.iata.org/ns/cargo#dgRadioactiveMaterial';

    /** Dimensions details */
    public const string dimensions = 'https://onerecord.iata.org/ns/cargo#dimensions';

    /**
     * Information about the Dimensions used for the rate described by the Line Item
     *
     * @deprecated since 3.2
     */
    public const string dimensionsForRate = 'https://onerecord.iata.org/ns/cargo#dimensionsForRate';

    /** Preferred unit for measurement and dimensions */
    public const string dimensionsUnit = 'https://onerecord.iata.org/ns/cargo#dimensionsUnit';

    /** Direction to indicate if it's Inbound or Outbound */
    public const string direction = 'https://onerecord.iata.org/ns/cargo#direction';

    /**
     * This is used as a discount to the “official” transportation charge on AWB to arrive at actual
     * selling price
     */
    public const string discount = 'https://onerecord.iata.org/ns/cargo#discount';

    /** Information about the calculated distance */
    public const string distanceCalculated = 'https://onerecord.iata.org/ns/cargo#distanceCalculated';

    /** Information about the measured distance */
    public const string distanceMeasured = 'https://onerecord.iata.org/ns/cargo#distanceMeasured';

    /** Unique document identifier */
    public const string documentIdentifier = 'https://onerecord.iata.org/ns/cargo#documentIdentifier';

    /** Link to the document, e.g. URL of the file where it is hosted */
    public const string documentLink = 'https://onerecord.iata.org/ns/cargo#documentLink';

    /** If no DocumentType provided, name of the referenced document */
    public const string documentName = 'https://onerecord.iata.org/ns/cargo#documentName';

    /** Type of the referenced document . Can refer UNEDIFACT 11 e.g. 740 - Air Waybill, but not limited to */
    public const string documentType = 'https://onerecord.iata.org/ns/cargo#documentType';

    /** Document version number */
    public const string documentVersion = 'https://onerecord.iata.org/ns/cargo#documentVersion';

    /** Linked documents to the person, e.g. driver's license, ID, etc. */
    public const string documents = 'https://onerecord.iata.org/ns/cargo#documents';

    /** Weight of dry ice */
    public const string dryIceWeight = 'https://onerecord.iata.org/ns/cargo#dryIceWeight';

    /** Earliest acceptance date time (requested or proposed) */
    public const string earliestAcceptanceTime = 'https://onerecord.iata.org/ns/cargo#earliestAcceptanceTime';

    /** Elevation from sea level - Change of data type to Value as of ontology v1.1 */
    public const string elevation = 'https://onerecord.iata.org/ns/cargo#elevation';

    /** Contains the Emergency contact name (e.g. the name of the agency) and phone number (min required) */
    public const string emergencyContact = 'https://onerecord.iata.org/ns/cargo#emergencyContact';

    /** Employee ID */
    public const string employeeId = 'https://onerecord.iata.org/ns/cargo#employeeId';

    /**
     * Entitlement code to define if charges are Due carrier (C) or Due agent (A). Refer to CXML Code List
     * 1.3
     */
    public const string entitlement = 'https://onerecord.iata.org/ns/cargo#entitlement';

    /** Reference to the Epermit of the consignment */
    public const string epermit = 'https://onerecord.iata.org/ns/cargo#epermit';

    /**
     * The original number is a unique number allocated to each document by the relevant Management
     * Authority. (box 1)
     */
    public const string epermitNumber = 'https://onerecord.iata.org/ns/cargo#epermitNumber';

    /**
     * Movement or milestone code. Can hold a named individual of the StatusCode core code list
     * (corresponding to cXML code list 1.18), but can also be referring to different code lists.
     */
    public const string eventCode = 'https://onerecord.iata.org/ns/cargo#eventCode';

    /** Date and time of the event */
    public const string eventDate = 'https://onerecord.iata.org/ns/cargo#eventDate';

    /** Refers to the URI of the linked object(s) */
    public const string eventFor = 'https://onerecord.iata.org/ns/cargo#eventFor';

    /** Location of event */
    public const string eventLocation = 'https://onerecord.iata.org/ns/cargo#eventLocation';

    /** If no EventCode provided, event name - e.g. Security clearance */
    public const string eventName = 'https://onerecord.iata.org/ns/cargo#eventName';

    /** Indicates type of event e.g. Scheduled, Estimated, Actual */
    public const string eventTimeType = 'https://onerecord.iata.org/ns/cargo#eventTimeType';

    /** Events object */
    public const string events = 'https://onerecord.iata.org/ns/cargo#events';

    /** Quantity measured by the examining authority (box 14) */
    public const string examiningQuantity = 'https://onerecord.iata.org/ns/cargo#examiningQuantity';

    /** The Rate at which the Air Waybill Amount has been multiplied to arrive at the amount of settlement. */
    public const string exchangeRate = 'https://onerecord.iata.org/ns/cargo#exchangeRate';

    /** Locations of excluded Via Points */
    public const string excludedViaPoints = 'https://onerecord.iata.org/ns/cargo#excludedViaPoints';

    /** Indicates an exclusive use shipment */
    public const string exclusiveUseIndicator = 'https://onerecord.iata.org/ns/cargo#exclusiveUseIndicator';

    /** Enum stating the status of the Activity */
    public const string executionStatus = 'https://onerecord.iata.org/ns/cargo#executionStatus';

    /**
     * Expected commodity of the shipment as per Commodity Code list. Either this or expected HS code
     * required
     */
    public const string expectedCommodity = 'https://onerecord.iata.org/ns/cargo#expectedCommodity';

    /**
     * Expected commodity of the shipment as per HS code (at least 6 digits). Either this or
     * expectedCommodityCode required
     */
    public const string expectedHScode = 'https://onerecord.iata.org/ns/cargo#expectedHScode';

    /** Product expiry date - e.g. for perishables goods or goods with programmed obsolescence */
    public const string expiryDate = 'https://onerecord.iata.org/ns/cargo#expiryDate';

    /**
     * Specifies the reference to the group which identifies the kind of substances and articles that are
     * deemed to be compatible. Mandatory field in case of transport of explosive articles or substances
     */
    public const string explosiveCompatibilityGroupCode = 'https://onerecord.iata.org/ns/cargo#explosiveCompatibilityGroupCode';

    /** Country of last re-export (box 12a). Refer ISO 3166-2 */
    public const string exportTradeCountry = 'https://onerecord.iata.org/ns/cargo#exportTradeCountry';

    /** References to all associated ExternalReferences */
    public const string externalReferences = 'https://onerecord.iata.org/ns/cargo#externalReferences';

    /** First name / given name */
    public const string firstName = 'https://onerecord.iata.org/ns/cargo#firstName';

    /** Indicates if Fissile is excepted */
    public const string fissileExceptionIndicator = 'https://onerecord.iata.org/ns/cargo#fissileExceptionIndicator';

    /** Fissile exception reference, mandatory if Fissile Exception Indicator is true. */
    public const string fissileExceptionReference = 'https://onerecord.iata.org/ns/cargo#fissileExceptionReference';

    /** Reference to the BookingOption the LogisticsObject is detailing */
    public const string forBookingOption = 'https://onerecord.iata.org/ns/cargo#forBookingOption';

    /** Reference to the BookingOptionRequest the information of the LogisticsObject is detailing */
    public const string forBookingOptionRequest = 'https://onerecord.iata.org/ns/cargo#forBookingOptionRequest';

    /** Reference to the Booking Request the of the Booking Option */
    public const string forBookingRequest = 'https://onerecord.iata.org/ns/cargo#forBookingRequest';

    /** Reference to the LiveAnimalsEpermit this Signature applies to */
    public const string forEpermit = 'https://onerecord.iata.org/ns/cargo#forEpermit';

    /** Reference to the Prices based on this Ratings */
    public const string forPrices = 'https://onerecord.iata.org/ns/cargo#forPrices';

    /** Reference to the ProductDg this DgProductRadioactive details */
    public const string forProductDg = 'https://onerecord.iata.org/ns/cargo#forProductDg';

    /** Information about the calculated fuel amount */
    public const string fuelAmountCalculated = 'https://onerecord.iata.org/ns/cargo#fuelAmountCalculated';

    /** Information about the measured fuel amount */
    public const string fuelAmountMeasured = 'https://onerecord.iata.org/ns/cargo#fuelAmountMeasured';

    /** e.g. Kerosene, Diesel, SAF, Electricity [renewable], Electricity [non-renewable] */
    public const string fuelType = 'https://onerecord.iata.org/ns/cargo#fuelType';

    /** Text holding an ULD Type Code if the Piece fulfills it before UnitComposition */
    public const string fulfillsUldTypeCode = 'https://onerecord.iata.org/ns/cargo#fulfillsUldTypeCode';

    /** Geolocation details */
    public const string geolocation = 'https://onerecord.iata.org/ns/cargo#geolocation';

    /**
     * Reference to the Location from which the Question was answered, relevant for split checks with
     * documentary and physical elements
     */
    public const string givenAtLocation = 'https://onerecord.iata.org/ns/cargo#givenAtLocation';

    /**
     * Description of goods, for the BookingShipment the commodity list defined by Modernizing Cargo
     * Distribution MCD working group can be used as a referential.
     */
    public const string goodsDescription = 'https://onerecord.iata.org/ns/cargo#goodsDescription';

    /**
     * Goods description used in the rate described by the Line Item
     *
     * @deprecated since 3.2
     */
    public const string goodsDescriptionForRate = 'https://onerecord.iata.org/ns/cargo#goodsDescriptionForRate';

    /** Appendix number of the convention (I, II or III) (box 1) */
    public const string goodsTypeCode = 'https://onerecord.iata.org/ns/cargo#goodsTypeCode';

    /** Appendix number of the convention (I, II or III) (box 1) */
    public const string goodsTypeExtensionCode = 'https://onerecord.iata.org/ns/cargo#goodsTypeExtensionCode';

    /** Total price */
    public const string grandTotal = 'https://onerecord.iata.org/ns/cargo#grandTotal';

    /** Weight details */
    public const string grossWeight = 'https://onerecord.iata.org/ns/cargo#grossWeight';

    /**
     * Gross weight for which the rate description details apply
     *
     * @deprecated since 3.2
     */
    public const string grossWeightForRate = 'https://onerecord.iata.org/ns/cargo#grossWeightForRate';

    /**
     * Exemption code - e.g. BIOM- Bio-Medical Samples SMUS - small undersized shipments MAIL - mail BIOM -
     * bio-medical samples DIPL - diplomatic bags or diplomatic mail LFSM - life-saving materials NUCL -
     * nuclear materials TRNS - transfer or transshipment
     */
    public const string groundsForExemption = 'https://onerecord.iata.org/ns/cargo#groundsForExemption';

    /**
     * Free text. This may include items such as Control temperature for substances stabilized by
     * temperature control, name and telephone number of a responsible person for infectious substances.
     */
    public const string handlingInformation = 'https://onerecord.iata.org/ns/cargo#handlingInformation';

    /**
     * Reference to the physical objects this Handling Service is performed on. To be used if a Service is
     * performed for a Location, TransportMeans, ULD or specific Piece or set of
     */
    public const string handlingServiceFor = 'https://onerecord.iata.org/ns/cargo#handlingServiceFor';

    /**
     * Identifies the hazard class / division identification containing a numeric field separated by a
     * decimal
     */
    public const string hazardClassificationId = 'https://onerecord.iata.org/ns/cargo#hazardClassificationId';

    /** Height */
    public const string height = 'https://onerecord.iata.org/ns/cargo#height';

    /** Refers to the Waybill(s) contained */
    public const string houseWaybills = 'https://onerecord.iata.org/ns/cargo#houseWaybills';

    /** Harmonized Commodity code, refer to hsType used. 6 minimum digits are expected. */
    public const string hsCode = 'https://onerecord.iata.org/ns/cargo#hsCode';

    /**
     * Harmonized Commodity code, refer to hsType used. 6 minimum digits are expected.
     *
     * @deprecated since 3.2
     */
    public const string hsCodeForRate = 'https://onerecord.iata.org/ns/cargo#hsCodeForRate';

    /** Commodity description */
    public const string hsCommodityDescription = 'https://onerecord.iata.org/ns/cargo#hsCommodityDescription';

    /** If no Code provided, name of commodity */
    public const string hsCommodityName = 'https://onerecord.iata.org/ns/cargo#hsCommodityName';

    /**
     * Reference identifying the type of standard code to be used for the Commodity Classification
     * (Brussels Tariff Nomenclature, EU Harmonized System Code, UN Standard International Trade
     * Classification). Mandatory if the commodity code is more than 6 digits
     */
    public const string hsType = 'https://onerecord.iata.org/ns/cargo#hsType';

    /** IATA accredited cargo agent 7 digit number */
    public const string iataCargoAgentCode = 'https://onerecord.iata.org/ns/cargo#iataCargoAgentCode';

    /** IATA CASS cargo agent 4 digit branch number / location identifier */
    public const string iataCargoAgentLocationIdentifier = 'https://onerecord.iata.org/ns/cargo#iataCargoAgentLocationIdentifier';

    /** Reference to the Piece this Item or Piece is contained in */
    public const string inPiece = 'https://onerecord.iata.org/ns/cargo#inPiece';

    public const string inUnitComposition = 'https://onerecord.iata.org/ns/cargo#inUnitComposition';

    /** Locations or stations to included in the routing */
    public const string includedViaPoints = 'https://onerecord.iata.org/ns/cargo#includedViaPoints';

    /**
     * Standard codes as defined by UNCEFACT and ICC that correspond to international rules for the
     * interpretation of the most commonly used trade terms in different countries. UNECE recommendation n.
     * 5 Incoterms 2.
     */
    public const string incoterms = 'https://onerecord.iata.org/ns/cargo#incoterms';

    /** Insurance details */
    public const string insurance = 'https://onerecord.iata.org/ns/cargo#insurance';

    /** Insured amount - amount covered by the insurance policy */
    public const string insuredAmount = 'https://onerecord.iata.org/ns/cargo#insuredAmount';

    /** Reference to the shipments insured */
    public const string insuredShipments = 'https://onerecord.iata.org/ns/cargo#insuredShipments';

    /** References to the Actions the object is involved in */
    public const string involvedInActions = 'https://onerecord.iata.org/ns/cargo#involvedInActions';

    /** Information about other Parties involved depending on the context of use */
    public const string involvedParties = 'https://onerecord.iata.org/ns/cargo#involvedParties';

    /** Id of each radionuclide or for mixtures of radionuclides. */
    public const string isotopeId = 'https://onerecord.iata.org/ns/cargo#isotopeId';

    /**
     * The name or symbol of each radionuclide or for mixtures of radionuclides, an appropriate general
     * description, or a list of the most restrictive radionuclides.
     */
    public const string isotopeName = 'https://onerecord.iata.org/ns/cargo#isotopeName';

    /** DgRadioactiveIsotope. */
    public const string isotopes = 'https://onerecord.iata.org/ns/cargo#isotopes';

    /** Name of person (or employee ID) who issued the security status */
    public const string issuedBy = 'https://onerecord.iata.org/ns/cargo#issuedBy';

    /** Reference to the Piece the document was issued for */
    public const string issuedForPiece = 'https://onerecord.iata.org/ns/cargo#issuedForPiece';

    /** Reference to the shipment the document was issued for */
    public const string issuedForShipment = 'https://onerecord.iata.org/ns/cargo#issuedForShipment';

    /** Reference to the Waybill object */
    public const string issuedForWaybill = 'https://onerecord.iata.org/ns/cargo#issuedForWaybill';

    /** Date and time when the security status was issued */
    public const string issuedOn = 'https://onerecord.iata.org/ns/cargo#issuedOn';

    /** Quantity of the item when applicable, with associated units of measure */
    public const string itemQuantity = 'https://onerecord.iata.org/ns/cargo#itemQuantity';

    /** Job title / position */
    public const string jobTitle = 'https://onerecord.iata.org/ns/cargo#jobTitle';

    /** Indication if shipper is a Known Shipper as per TSA grant */
    public const string knownShipper = 'https://onerecord.iata.org/ns/cargo#knownShipper';

    /** Last name / family name / surname */
    public const string lastName = 'https://onerecord.iata.org/ns/cargo#lastName';

    /** Latest Acceptance time as per CargoIQ definition (requested, proposed or actual) */
    public const string latestAcceptanceTime = 'https://onerecord.iata.org/ns/cargo#latestAcceptanceTime';

    /** Latest arrival time at destination */
    public const string latestArrivalTime = 'https://onerecord.iata.org/ns/cargo#latestArrivalTime';

    /** Location latitude decimal */
    public const string latitude = 'https://onerecord.iata.org/ns/cargo#latitude';

    /** Leg number */
    public const string legNumber = 'https://onerecord.iata.org/ns/cargo#legNumber';

    /**
     * Reference to an ExternalReference holding a legacy template outside of ONE Record, such as a photo
     * or pdf of a checksheet
     */
    public const string legacyTemplate = 'https://onerecord.iata.org/ns/cargo#legacyTemplate';

    /** Length */
    public const string length = 'https://onerecord.iata.org/ns/cargo#length';

    /** Number of the line item */
    public const string lineItemNumber = 'https://onerecord.iata.org/ns/cargo#lineItemNumber';

    /** References to piece groupings for rating */
    public const string lineItemPackages = 'https://onerecord.iata.org/ns/cargo#lineItemPackages';

    /** Load type of the shipment or piece (Bulk, ULD, Pallet, Loose) */
    public const string loadType = 'https://onerecord.iata.org/ns/cargo#loadType';

    /** References to Materials onloaded or offloaded */
    public const string loadedMaterials = 'https://onerecord.iata.org/ns/cargo#loadedMaterials';

    /** References to Pieces onloaded or offloaded */
    public const string loadedPieces = 'https://onerecord.iata.org/ns/cargo#loadedPieces';

    /** References to LoadingUnits onloaded or offloaded */
    public const string loadedUnits = 'https://onerecord.iata.org/ns/cargo#loadedUnits';

    /** References to all actions of type Loading performed for the TransportMovement */
    public const string loadingActions = 'https://onerecord.iata.org/ns/cargo#loadingActions';

    /**
     * ULD height or loading limitation code. Refer CXML Code List 1.47, e.g. R - ULD Height above 244
     * centimetres
     */
    public const string loadingIndicator = 'https://onerecord.iata.org/ns/cargo#loadingIndicator';

    /** Short text stating the loading position in the TransportMeans */
    public const string loadingPositionIdentifier = 'https://onerecord.iata.org/ns/cargo#loadingPositionIdentifier';

    /** Enum stating whether the LoadingAction describes onloading or offloading */
    public const string loadingType = 'https://onerecord.iata.org/ns/cargo#loadingType';

    /** Reference to the LoadingUnit composed in the Unit Composition or referenced in Composing actions */
    public const string loadingUnit = 'https://onerecord.iata.org/ns/cargo#loadingUnit';

    /**
     * Location code of airport, freight terminal, seaport, rail station. UN/LOCODE city code (5 letter) or
     * IATA airport code (3 letter)
     */
    public const string locationCodes = 'https://onerecord.iata.org/ns/cargo#locationCodes';

    /** String indicating if the Other Charge Location is Origin (O) or Transit (T) or Destination(D) */
    public const string locationIndicator = 'https://onerecord.iata.org/ns/cargo#locationIndicator';

    /** Full name of the location */
    public const string locationName = 'https://onerecord.iata.org/ns/cargo#locationName';

    /** Location type - e.g. Airport, Freight terminal, Rail station, Seaport, etc */
    public const string locationType = 'https://onerecord.iata.org/ns/cargo#locationType';

    /** Long text of the question */
    public const string longText = 'https://onerecord.iata.org/ns/cargo#longText';

    /** Location longitude decimal */
    public const string longitude = 'https://onerecord.iata.org/ns/cargo#longitude';

    /** Production lot number / reference */
    public const string lotNumber = 'https://onerecord.iata.org/ns/cargo#lotNumber';

    /** A notation that the material is low dispersible radioactive material. */
    public const string lowDispersibleIndicator = 'https://onerecord.iata.org/ns/cargo#lowDispersibleIndicator';

    /** Manufacturing company details and contacts */
    public const string manufacturer = 'https://onerecord.iata.org/ns/cargo#manufacturer';

    /** Reference to the master Waybill if it is contained in one */
    public const string masterWaybill = 'https://onerecord.iata.org/ns/cargo#masterWaybill';

    /** Model of the LoadingMaterial if any */
    public const string materialModel = 'https://onerecord.iata.org/ns/cargo#materialModel';

    /** Type of the LoadingMaterial */
    public const string materialType = 'https://onerecord.iata.org/ns/cargo#materialType';

    /** Maximum number of segments for the transportation of the goods. 1 means direct flight */
    public const string maxSegments = 'https://onerecord.iata.org/ns/cargo#maxSegments';

    /** Maximum temperature of the range */
    public const string maxTemperature = 'https://onerecord.iata.org/ns/cargo#maxTemperature';

    /** Maximum quantity */
    public const string maximumQuantity = 'https://onerecord.iata.org/ns/cargo#maximumQuantity';

    /** Timestamp for the measurement */
    public const string measurementTimestamp = 'https://onerecord.iata.org/ns/cargo#measurementTimestamp';

    /** Information about all non-Geolocation values of the measurement */
    public const string measurementValue = 'https://onerecord.iata.org/ns/cargo#measurementValue';

    /** Reference to the Measurements recorded by the Sensor */
    public const string measurements = 'https://onerecord.iata.org/ns/cargo#measurements';

    /** Name of the CO2 calculation method */
    public const string methodName = 'https://onerecord.iata.org/ns/cargo#methodName';

    /** Version used for the calculation */
    public const string methodVersion = 'https://onerecord.iata.org/ns/cargo#methodVersion';

    /** Middle name/ other name */
    public const string middleName = 'https://onerecord.iata.org/ns/cargo#middleName';

    /** Minimum temperature of the range */
    public const string minTemperature = 'https://onerecord.iata.org/ns/cargo#minTemperature';

    /** Minimum quantity */
    public const string minimumQuantity = 'https://onerecord.iata.org/ns/cargo#minimumQuantity';

    /**
     * Mode of transport code, refer to UNECE Rec. 19
     * https://unece.org/fileadmin/DAM/cefact/recommendations/rec19/rec19_1cf19e.pdf
     */
    public const string modeCode = 'https://onerecord.iata.org/ns/cargo#modeCode';

    /** Pre-Carriage, Main-Carriage or On-Carriage */
    public const string modeQualifier = 'https://onerecord.iata.org/ns/cargo#modeQualifier';

    /** The check is a Modular 7 validation on the AWB number, recorded as a boolean. */
    public const string modularCheckNumber = 'https://onerecord.iata.org/ns/cargo#modularCheckNumber';

    /**
     * The milestone list still needs to be defined, it includes elements from CXML Code List 1.92 but is
     * not limited to those values, e.g. block-on and block-off times might be added as a comparison to
     * wheels off and touchdown.
     */
    public const string movementMilestone = 'https://onerecord.iata.org/ns/cargo#movementMilestone';

    /** The type of time can be Actual, Estimated ot Scheduled */
    public const string movementTimeType = 'https://onerecord.iata.org/ns/cargo#movementTimeType';

    /** Information about times related to the movement (milestone list to be defined) */
    public const string movementTimes = 'https://onerecord.iata.org/ns/cargo#movementTimes';

    /**
     * Timestamp (date and time) of the movement time. If the movement time is recorded asynchronously, the
     * timestamp should reflect the actual time, not when the data was created.
     */
    public const string movementTimestamp = 'https://onerecord.iata.org/ns/cargo#movementTimestamp';

    /** Human-understandable name of object depending on the context */
    public const string name = 'https://onerecord.iata.org/ns/cargo#name';

    /** Number of corrections to CASS records */
    public const string nbCorrections = 'https://onerecord.iata.org/ns/cargo#nbCorrections';

    /**
     * The total net weight of dangerous goods transported of this line item. For air transport the value
     * must be the volume or mass in each package.
     */
    public const string netWeightMeasure = 'https://onerecord.iata.org/ns/cargo#netWeightMeasure';

    /** Free text for customs remarks, not used in OCI Composition Rules Table */
    public const string note = 'https://onerecord.iata.org/ns/cargo#note';

    /**
     * Reference to the organization that was notified as part of a status update event
     *
     * @since 3.3
     */
    public const string notifiedOrganization = 'https://onerecord.iata.org/ns/cargo#notifiedOrganization';

    /** Number of doors */
    public const string numberOfDoors = 'https://onerecord.iata.org/ns/cargo#numberOfDoors';

    /** Number of fittings */
    public const string numberOfFittings = 'https://onerecord.iata.org/ns/cargo#numberOfFittings';

    /** Number of nets */
    public const string numberOfNets = 'https://onerecord.iata.org/ns/cargo#numberOfNets';

    /** Number of straps */
    public const string numberOfStraps = 'https://onerecord.iata.org/ns/cargo#numberOfStraps';

    /** Numerical value */
    public const string numericalValue = 'https://onerecord.iata.org/ns/cargo#numericalValue';

    /**
     * When no value is declared for Carriage, this field may be completed with the value TRUE otherwise
     * FALSE
     */
    public const string nvdForCarriage = 'https://onerecord.iata.org/ns/cargo#nvdForCarriage';

    /**
     * When no value is declared for Customs, this field may be completed with the value TRUE otherwise
     * FALSE
     */
    public const string nvdForCustoms = 'https://onerecord.iata.org/ns/cargo#nvdForCustoms';

    /** Integer holding the oci line number when upcasting multi-line oci structures from CIMP/CXML */
    public const string ociLineNumber = 'https://onerecord.iata.org/ns/cargo#ociLineNumber';

    /**
     * Contains two designator codes of ODLN or Operational Damage Limit Notices. ODLN code is used to
     * define type of damage after visually check the serviceability of ULDs section 7, Standard
     * Specifications 4/3 or 4/4 in ULD Regulations
     */
    public const string odlnCode = 'https://onerecord.iata.org/ns/cargo#odlnCode';

    /** Reference to the Product describing the Item */
    public const string ofProduct = 'https://onerecord.iata.org/ns/cargo#ofProduct';

    /** Reference to the Shipment the Piece is assigned to */
    public const string ofShipment = 'https://onerecord.iata.org/ns/cargo#ofShipment';

    /** Date and time of beginning of offer validity */
    public const string offerValidFrom = 'https://onerecord.iata.org/ns/cargo#offerValidFrom';

    /** Date and time of end of offer validity */
    public const string offerValidTo = 'https://onerecord.iata.org/ns/cargo#offerValidTo';

    /** Reference to the TransportMeans that is being onloaded or offloaded */
    public const string onTransportMeans = 'https://onerecord.iata.org/ns/cargo#onTransportMeans';

    /** References to the Actions happening at the Location */
    public const string onsiteActions = 'https://onerecord.iata.org/ns/cargo#onsiteActions';

    /** Transport Movement on which the Transport Means is used */
    public const string operatedTransportMovement = 'https://onerecord.iata.org/ns/cargo#operatedTransportMovement';

    /**
     * Information about the parties operating this TransportMovement, for example pilot and co-pilot; can
     * also refer to organizations through Party
     */
    public const string operatingParties = 'https://onerecord.iata.org/ns/cargo#operatingParties';

    /** Reference to the TransportMeans operating the TransportMovement */
    public const string operatingTransportMeans = 'https://onerecord.iata.org/ns/cargo#operatingTransportMeans';

    /** Issuing date for Origin reference permit or re-export reference Certificate (box 12) */
    public const string originReferencePermitDateTime = 'https://onerecord.iata.org/ns/cargo#originReferencePermitDateTime';

    /** identifier of Origin reference permit or re-export reference Certificate (box 12/12a) */
    public const string originReferencePermitId = 'https://onerecord.iata.org/ns/cargo#originReferencePermitId';

    /** Document type code of origin reference permit or re-export reference Certificate (box 12/12a) */
    public const string originReferencePermitTypeCode = 'https://onerecord.iata.org/ns/cargo#originReferencePermitTypeCode';

    /** country of origin (box 12). Refer ISO 3166-2 */
    public const string originTradeCountry = 'https://onerecord.iata.org/ns/cargo#originTradeCountry';

    /** Document originator details and contacts */
    public const string originator = 'https://onerecord.iata.org/ns/cargo#originator';

    /** Characteristics of the product */
    public const string otherCharacteristics = 'https://onerecord.iata.org/ns/cargo#otherCharacteristics';

    /** Other Charge amount */
    public const string otherChargeAmount = 'https://onerecord.iata.org/ns/cargo#otherChargeAmount';

    /** Refer to CargoXML Code List 1.2 for Other Charges */
    public const string otherChargeCode = 'https://onerecord.iata.org/ns/cargo#otherChargeCode';

    /** Information about Other Charges applying to this Waybill */
    public const string otherCharges = 'https://onerecord.iata.org/ns/cargo#otherCharges';

    /**
     * Indicator whether the payment of Other Charges is to be made at origin (prepaid) or at destination
     * (collect) as per bullet point 13 - data element 15a/15b from AWB
     */
    public const string otherChargesIndicator = 'https://onerecord.iata.org/ns/cargo#otherChargesIndicator';

    /** Supplementary Customs, Security and Regulatory Control Information */
    public const string otherCustomsInformation = 'https://onerecord.iata.org/ns/cargo#otherCustomsInformation';

    /** Identifier type or description */
    public const string otherIdentifierType = 'https://onerecord.iata.org/ns/cargo#otherIdentifierType';

    /** Details about any other identifier, depending on the context of use */
    public const string otherIdentifiers = 'https://onerecord.iata.org/ns/cargo#otherIdentifiers';

    /**
     * Any other regulated entity that accepts custody of the cargo and accepts the security status
     * originally issued
     */
    public const string otherRegulatedEntities = 'https://onerecord.iata.org/ns/cargo#otherRegulatedEntities';

    /** Other methods used to secure the cargo */
    public const string otherScreeningMethods = 'https://onerecord.iata.org/ns/cargo#otherScreeningMethods';

    /**
     * Applies to fissile material only, other than fissile excepted. A numeric value expressed to one
     * decimal place preceded by the letters CSI.
     */
    public const string overpackCriticalitySafetyIndexNumeric = 'https://onerecord.iata.org/ns/cargo#overpackCriticalitySafetyIndexNumeric';

    /** Overpack indicator */
    public const string overpackIndicator = 'https://onerecord.iata.org/ns/cargo#overpackIndicator';

    /**
     * A single number assigned to a package, overpack or freight container to provide control over
     * radiation exposure.
     */
    public const string overpackT1 = 'https://onerecord.iata.org/ns/cargo#overpackT1';

    /**
     * Identifies the Logistic Unit package type. UN Recommendation on Transport of Dangerous Goods, Model
     * Regulations
     */
    public const string overpackTypeCode = 'https://onerecord.iata.org/ns/cargo#overpackTypeCode';

    /** Owner code of the ULD in aa, an or na format - owner can be an airline or leasing company */
    public const string ownerCode = 'https://onerecord.iata.org/ns/cargo#ownerCode';

    /** Reference to the Organization for which the RegulatedEntity information is valid */
    public const string owningOrganization = 'https://onerecord.iata.org/ns/cargo#owningOrganization';

    /** Information about the total weight of a grouping of pieces */
    public const string packageGrossWeight = 'https://onerecord.iata.org/ns/cargo#packageGrossWeight';

    /** Reference identifying how the package is marked. Field is hardcode to "SSCC-18", "UPC" or "Other" */
    public const string packageMarkCoded = 'https://onerecord.iata.org/ns/cargo#packageMarkCoded';

    /** Integer holding the total slac of a grouping of pieces */
    public const string packageSlac = 'https://onerecord.iata.org/ns/cargo#packageSlac';

    /** Information about the total volume of a grouping of pieces */
    public const string packageVolume = 'https://onerecord.iata.org/ns/cargo#packageVolume';

    /** SSCC-18 code for the value of the package mark, company or bar code, free text, pallet code, etc. */
    public const string packagedeIdentifier = 'https://onerecord.iata.org/ns/cargo#packagedeIdentifier';

    /** Packing group, If used must reference I, II or III */
    public const string packagingDangerLevelCode = 'https://onerecord.iata.org/ns/cargo#packagingDangerLevelCode';

    /** Packaging details */
    public const string packagingType = 'https://onerecord.iata.org/ns/cargo#packagingType';

    /**
     * The packing instruction number applicable to the UN number / proper shipping name entry. A
     * three-numeric value which may be preceded by the letter Y. Mandatory field for air transport (Air)
     */
    public const string packingInstructionNumber = 'https://onerecord.iata.org/ns/cargo#packingInstructionNumber';

    /** Reference to the parent Organization */
    public const string parentOrganization = 'https://onerecord.iata.org/ns/cargo#parentOrganization';

    /** Reference to the IoT Device to which the sensor is linked */
    public const string partOfIotDevice = 'https://onerecord.iata.org/ns/cargo#partOfIotDevice';

    /**
     * Boolean indicating that the LogisticsEvent is only applicable for parts of the LogisticObject it was
     * recorded for, for example for some Pieces of a Shipment
     */
    public const string partialEventIndicator = 'https://onerecord.iata.org/ns/cargo#partialEventIndicator';

    /** Reference to the Agent described by the role of the Party */
    public const string partyDetails = 'https://onerecord.iata.org/ns/cargo#partyDetails';

    /** Role fo the Company in the context. Can refer to Code List 1.36 in the CXML Toolkit */
    public const string partyRole = 'https://onerecord.iata.org/ns/cargo#partyRole';

    /** Boolean indicating whether the Check was passed */
    public const string passed = 'https://onerecord.iata.org/ns/cargo#passed';

    /** Reference to the Location the Action was performed at */
    public const string performedAt = 'https://onerecord.iata.org/ns/cargo#performedAt';

    /** Code specifying the document name. (box 1) */
    public const string permitTypeCode = 'https://onerecord.iata.org/ns/cargo#permitTypeCode';

    /** Description if TypeCode is Other (box 1) */
    public const string permitTypeOtherDescription = 'https://onerecord.iata.org/ns/cargo#permitTypeOtherDescription';

    /** A description of the physical and chemical form of the material. */
    public const string physicalChemicalForm = 'https://onerecord.iata.org/ns/cargo#physicalChemicalForm';

    /**
     * Number of pieces for which the rate description details apply
     *
     * @deprecated since 3.2
     */
    public const string pieceCountForRate = 'https://onerecord.iata.org/ns/cargo#pieceCountForRate';

    /** Number of pieces in the piece group */
    public const string pieceGroupCount = 'https://onerecord.iata.org/ns/cargo#pieceGroupCount';

    /** Total gross weight of the piece group */
    public const string pieceGroupGrossWeight = 'https://onerecord.iata.org/ns/cargo#pieceGroupGrossWeight';

    /** Identifier of the piece group, increasing integers */
    public const string pieceGroupId = 'https://onerecord.iata.org/ns/cargo#pieceGroupId';

    /** Reference to the Piece groups of the shipment */
    public const string pieceGroups = 'https://onerecord.iata.org/ns/cargo#pieceGroups';

    /** Height of a single piece */
    public const string pieceHeight = 'https://onerecord.iata.org/ns/cargo#pieceHeight';

    /** Length of a single piece */
    public const string pieceLength = 'https://onerecord.iata.org/ns/cargo#pieceLength';

    /** References to Pieces for which a rate applies */
    public const string pieceReferences = 'https://onerecord.iata.org/ns/cargo#pieceReferences';

    /** Weight of a single piece */
    public const string pieceWeight = 'https://onerecord.iata.org/ns/cargo#pieceWeight';

    /** Width of a single piece */
    public const string pieceWidth = 'https://onerecord.iata.org/ns/cargo#pieceWidth';

    /** References to the Pieces that are part of this Shipment */
    public const string pieces = 'https://onerecord.iata.org/ns/cargo#pieces';

    /** Post Office box number / code */
    public const string postOfficeBox = 'https://onerecord.iata.org/ns/cargo#postOfficeBox';

    /**
     * Postal / ZIP code
     *
     * @deprecated since 3.2
     */
    public const string postalCode = 'https://onerecord.iata.org/ns/cargo#postalCode';

    /**
     * When part of the Request it refers to the preferred Transport ID from the customer. When part of the
     * BookingOption (offer or actual booking) it refers to the expected Transport ID or flight
     */
    public const string preferredTransportId = 'https://onerecord.iata.org/ns/cargo#preferredTransportId';

    /** IATA three-numeric airline prefix number */
    public const string prefix = 'https://onerecord.iata.org/ns/cargo#prefix';

    /** Price of the Booking (if different from the offer) */
    public const string price = 'https://onerecord.iata.org/ns/cargo#price';

    /** Reference to a price reference if existing (e.g. Allotment number, contract reference, etc.) */
    public const string priceReferenceId = 'https://onerecord.iata.org/ns/cargo#priceReferenceId';

    /** Specification of the price e.g. Street, Group, Spot, etc. */
    public const string priceSpecification = 'https://onerecord.iata.org/ns/cargo#priceSpecification';

    /** Carrier's product code */
    public const string productCode = 'https://onerecord.iata.org/ns/cargo#productCode';

    /** Carrier's product description */
    public const string productDescription = 'https://onerecord.iata.org/ns/cargo#productDescription';

    /** Production country details. Refer ISO 3166-2 */
    public const string productionCountry = 'https://onerecord.iata.org/ns/cargo#productionCountry';

    /**
     * Production country for the rate described by this Line Item. Refer ISO 3166-2
     *
     * @deprecated since 3.2
     */
    public const string productionCountryForRate = 'https://onerecord.iata.org/ns/cargo#productionCountryForRate';

    /** Production date */
    public const string productionDate = 'https://onerecord.iata.org/ns/cargo#productionDate';

    /**
     * The name used to describe the particular article or substance as shown in the UN Model Regulations
     * Dangerous Goods List
     */
    public const string properShippingName = 'https://onerecord.iata.org/ns/cargo#properShippingName';

    /**
     * Most instances of all packed in one will require the addition of the Q value which 1. Applies to air
     * transport only. (Air)
     */
    public const string qValueNumeric = 'https://onerecord.iata.org/ns/cargo#qValueNumeric';

    /** Quantity for the charge if applicable */
    public const string quantity = 'https://onerecord.iata.org/ns/cargo#quantity';

    /** Quantity including units (box 11) */
    public const string quantityAnimals = 'https://onerecord.iata.org/ns/cargo#quantityAnimals';

    /** Product quantity for unit price - e.g. 12 (eggs for one USD 1) */
    public const string quantityForUnitPrice = 'https://onerecord.iata.org/ns/cargo#quantityForUnitPrice';

    /** Reference to the Question the Answer is for */
    public const string question = 'https://onerecord.iata.org/ns/cargo#question';

    /** Number of the Question within the template (alphanumeric) */
    public const string questionNumber = 'https://onerecord.iata.org/ns/cargo#questionNumber';

    /** Section of the CheckTemplate this Question is part of */
    public const string questionSection = 'https://onerecord.iata.org/ns/cargo#questionSection';

    /** References to all Questions that are part of this template */
    public const string questions = 'https://onerecord.iata.org/ns/cargo#questions';

    /** Reference to the ranges */
    public const string ranges = 'https://onerecord.iata.org/ns/cargo#ranges';

    /** TACT Rate for rate description details */
    public const string rateCharge = 'https://onerecord.iata.org/ns/cargo#rateCharge';

    /** Rate class code e.g. Q. Refer to CXML Code List 1.4 Rate Class Codes */
    public const string rateClassCode = 'https://onerecord.iata.org/ns/cargo#rateClassCode';

    /** Rate Surcharge/Reduction - Basic Rate Class Code */
    public const string rateClassCodeBasic = 'https://onerecord.iata.org/ns/cargo#rateClassCodeBasic';

    /** Information about the total gross weight considered for a rate */
    public const string rateGrossWeight = 'https://onerecord.iata.org/ns/cargo#rateGrossWeight';

    /** Rate Surcharge/Reduction - Percentage of red. / surcharge */
    public const string ratePercentage = 'https://onerecord.iata.org/ns/cargo#ratePercentage';

    /** Integer holding the total slac considered for a rate */
    public const string rateSlac = 'https://onerecord.iata.org/ns/cargo#rateSlac';

    /** Information about the total volume considered for a rate */
    public const string rateVolume = 'https://onerecord.iata.org/ns/cargo#rateVolume';

    /** Rating used for pricing */
    public const string ratings = 'https://onerecord.iata.org/ns/cargo#ratings';

    /** IATA 3-letter city code of the rate combination point as defined in TACT */
    public const string rcp = 'https://onerecord.iata.org/ns/cargo#rcp';

    /** String describing the reason for a charge */
    public const string reasonDescription = 'https://onerecord.iata.org/ns/cargo#reasonDescription';

    /** A free text for user to include a reason for correction */
    public const string reasonsForAdjustments = 'https://onerecord.iata.org/ns/cargo#reasonsForAdjustments';

    /**
     * Regulated entity that tendered the consignment
     *
     * @deprecated since 3.3
     */
    public const string receivedFrom = 'https://onerecord.iata.org/ns/cargo#receivedFrom';

    /** Reference to the Geolocation recorded of the measurement */
    public const string recordedGeolocation = 'https://onerecord.iata.org/ns/cargo#recordedGeolocation';

    /**
     * Integer holding the recorded piece count of a status update event. Mandatory if the status update
     * event is partial.
     *
     * @since 3.3
     */
    public const string recordedPieceCount = 'https://onerecord.iata.org/ns/cargo#recordedPieceCount';

    /**
     * Information about the recorded volume of a status update event
     *
     * @since 3.3
     */
    public const string recordedVolume = 'https://onerecord.iata.org/ns/cargo#recordedVolume';

    /**
     * Information of the recorded weight of a status update event
     *
     * @since 3.3
     */
    public const string recordedWeight = 'https://onerecord.iata.org/ns/cargo#recordedWeight';

    /** Reference to the Actor recording the LogisticsEvent */
    public const string recordingActor = 'https://onerecord.iata.org/ns/cargo#recordingActor';

    /** Organization recording the LogisticsEvent */
    public const string recordingOrganization = 'https://onerecord.iata.org/ns/cargo#recordingOrganization';

    /** References to the LogisticsObjects referring to this external reference */
    public const string referenceForObjects = 'https://onerecord.iata.org/ns/cargo#referenceForObjects';

    /** Refers to the Booking */
    public const string referredBookingOption = 'https://onerecord.iata.org/ns/cargo#referredBookingOption';

    /** Region/ State / Department. Refer ISO 3166-2 */
    public const string regionCode = 'https://onerecord.iata.org/ns/cargo#regionCode';

    /**
     * References to the regulated entities the shipment or piece was received from
     *
     * @since 3.3
     */
    public const string regulatedEntitiesReceivedFrom = 'https://onerecord.iata.org/ns/cargo#regulatedEntitiesReceivedFrom';

    /** Information about the accepting regulated entity of the Security Declaration */
    public const string regulatedEntityAcceptor = 'https://onerecord.iata.org/ns/cargo#regulatedEntityAcceptor';

    /** Category code of the Regulated Entity */
    public const string regulatedEntityCategory = 'https://onerecord.iata.org/ns/cargo#regulatedEntityCategory';

    /** Expiry date 4 digits month/year */
    public const string regulatedEntityExpiryDate = 'https://onerecord.iata.org/ns/cargo#regulatedEntityExpiryDate';

    /** Regulated entity identifier as per IATA e-CSD/CSD Resolution 65 */
    public const string regulatedEntityIdentifier = 'https://onerecord.iata.org/ns/cargo#regulatedEntityIdentifier';

    /** Regulated entity issuing the Security Declaration */
    public const string regulatedEntityIssuer = 'https://onerecord.iata.org/ns/cargo#regulatedEntityIssuer';

    /** Remarks or Supplement Information */
    public const string remarks = 'https://onerecord.iata.org/ns/cargo#remarks';

    /** Details of the remarks, mandatory */
    public const string remarksText = 'https://onerecord.iata.org/ns/cargo#remarksText';

    /** Reportable quantities, To and from the USA only */
    public const string reportableQuantity = 'https://onerecord.iata.org/ns/cargo#reportableQuantity';

    /** Indicates if the Booking Option is a match to the Booking Option Request preferences */
    public const string requestMatch = 'https://onerecord.iata.org/ns/cargo#requestMatch';

    public const string resultOfCheck = 'https://onerecord.iata.org/ns/cargo#resultOfCheck';

    /** Information about a result Value of any kind of the Check */
    public const string resultValue = 'https://onerecord.iata.org/ns/cargo#resultValue';

    /** Salutation */
    public const string salutation = 'https://onerecord.iata.org/ns/cargo#salutation';

    /**
     * Screening methods which have been used to secure the cargo PHS – Physical Inspection and/or hand
     * search VCK - Visual check XRY- X-ray equipment EDS - Explosive detection system EDD - Explosive
     * detection dogsETD - Explosive trace detection equipment - particles or vapor CMD - Cargo metal
     * detection AOM - Subjected to any other means: this entry should be followed by free text specifying
     * what other mean was used to secure the cargo
     */
    public const string screeningMethods = 'https://onerecord.iata.org/ns/cargo#screeningMethods';

    /** Seal identifier */
    public const string seal = 'https://onerecord.iata.org/ns/cargo#seal';

    /** ULD seal number if applicable */
    public const string sealNumber = 'https://onerecord.iata.org/ns/cargo#sealNumber';

    /** Security details of the piece */
    public const string securityDeclarations = 'https://onerecord.iata.org/ns/cargo#securityDeclarations';

    /** Security Stamp ID */
    public const string securityStampId = 'https://onerecord.iata.org/ns/cargo#securityStampId';

    /** Security status indicator (CXML 1.13) - e.g. SPX- Cargo Secure for Passenger and All-Cargo Aircraft */
    public const string securityStatus = 'https://onerecord.iata.org/ns/cargo#securityStatus';

    /** Type of sensor as described in Interactive Cargo Recommended Practice */
    public const string sensorType = 'https://onerecord.iata.org/ns/cargo#sensorType';

    /** Short text to detail sequence number (alphanumeric) */
    public const string sequenceNumber = 'https://onerecord.iata.org/ns/cargo#sequenceNumber';

    /** Serial number that allows to uniquely identify the object */
    public const string serialNumber = 'https://onerecord.iata.org/ns/cargo#serialNumber';

    /** Reference to the Activity the Action was performed for */
    public const string servedActivity = 'https://onerecord.iata.org/ns/cargo#servedActivity';

    /** Reference to Services this Activity is executed for */
    public const string servedServices = 'https://onerecord.iata.org/ns/cargo#servedServices';

    /** One letter service code as per bullet point 18.4 - data element 22Z from AWB */
    public const string serviceCode = 'https://onerecord.iata.org/ns/cargo#serviceCode';

    /**
     * Reference to the Waybills this service is to be performed for. To be used if a service is to be
     * performed for a specific shipment or set of
     */
    public const string serviceForWaybills = 'https://onerecord.iata.org/ns/cargo#serviceForWaybills';

    /** Service level code */
    public const string serviceLevelCode = 'https://onerecord.iata.org/ns/cargo#serviceLevelCode';

    /** Reference to the Party providing the service */
    public const string serviceProvider = 'https://onerecord.iata.org/ns/cargo#serviceProvider';

    /** Reference to the Party requesting the service */
    public const string serviceRequestor = 'https://onerecord.iata.org/ns/cargo#serviceRequestor';

    /** Designator of serviceability condition e.g. SER or DAM */
    public const string serviceabilityCode = 'https://onerecord.iata.org/ns/cargo#serviceabilityCode';

    /** Reference to the Shipment */
    public const string shipment = 'https://onerecord.iata.org/ns/cargo#shipment';

    /**
     * Contains the shipper's declaration to comply with the regulations text note. Free text . This field
     * is mandatory for air (Air)
     */
    public const string shipperDeclarationText = 'https://onerecord.iata.org/ns/cargo#shipperDeclarationText';

    /** The shipper or its Agent may enter the appropriate optional shipping */
    public const string shippingInfo = 'https://onerecord.iata.org/ns/cargo#shippingInfo';

    /** Shipping marks */
    public const string shippingMarks = 'https://onerecord.iata.org/ns/cargo#shippingMarks';

    /** Optional shipping reference number if any */
    public const string shippingRefNo = 'https://onerecord.iata.org/ns/cargo#shippingRefNo';

    /** Short name of the Organization if any */
    public const string shortName = 'https://onerecord.iata.org/ns/cargo#shortName';

    /** Short text of the Question */
    public const string shortText = 'https://onerecord.iata.org/ns/cargo#shortText';

    /** Signatory company name */
    public const string signatoryCompany = 'https://onerecord.iata.org/ns/cargo#signatoryCompany';

    /**
     * Role of the signatory with regards to the ePermit: Applicant, Permit issuer, Issuing Authority or
     * Examining authority
     */
    public const string signatoryRole = 'https://onerecord.iata.org/ns/cargo#signatoryRole';

    /** Date and time of the signature */
    public const string signatureDate = 'https://onerecord.iata.org/ns/cargo#signatureDate';

    /** Signatory signature authentication text */
    public const string signatureStatement = 'https://onerecord.iata.org/ns/cargo#signatureStatement';

    /** Code specifying a type of government action such as inspection, detention, fumigation, security. */
    public const string signatureTypeCode = 'https://onerecord.iata.org/ns/cargo#signatureTypeCode';

    /**
     * List of all the signatures of the Epermit (applicant box 4, issuing authority box 6, issuer box 13
     * and examining authority box 14)
     */
    public const string signatures = 'https://onerecord.iata.org/ns/cargo#signatures';

    /** Indicator whether a logistics object is a skeleton object */
    public const string skeletonIndicator = 'https://onerecord.iata.org/ns/cargo#skeletonIndicator';

    /** Shipper's Load And Count ( total contained piece count as provided by shipper) */
    public const string slac = 'https://onerecord.iata.org/ns/cargo#slac';

    /**
     * Slac used for the rate described by the Line item
     *
     * @deprecated since 3.2
     */
    public const string slacForRate = 'https://onerecord.iata.org/ns/cargo#slacForRate';

    /** Current status of the space allocation of the booking segment */
    public const string spaceAllocationCode = 'https://onerecord.iata.org/ns/cargo#spaceAllocationCode';

    /** Special conditions (box 5) */
    public const string specialConditions = 'https://onerecord.iata.org/ns/cargo#specialConditions';

    /** A notation that the material is special form */
    public const string specialFormIndicator = 'https://onerecord.iata.org/ns/cargo#specialFormIndicator';

    /** Three-letter special handling code (SPH) */
    public const string specialHandlingCodes = 'https://onerecord.iata.org/ns/cargo#specialHandlingCodes';

    /**
     * For Air Mode: Special Provision may show a single, double or triple digit number preceded by the
     * letter A, against appropriate entries in the List of Dangerous Goods
     */
    public const string specialProvisionId = 'https://onerecord.iata.org/ns/cargo#specialProvisionId';

    /** Special service requests */
    public const string specialServiceRequests = 'https://onerecord.iata.org/ns/cargo#specialServiceRequests';

    /** Species common name (box 8) */
    public const string speciesCommonName = 'https://onerecord.iata.org/ns/cargo#speciesCommonName';

    /** Species scientific name (box 7) */
    public const string speciesScientificName = 'https://onerecord.iata.org/ns/cargo#speciesScientificName';

    /** Description of specimens, including age and sex if LA (box 9) */
    public const string specimenDescription = 'https://onerecord.iata.org/ns/cargo#specimenDescription';

    /** Description of specimens, CITES type code (box 9) */
    public const string specimenTypeCode = 'https://onerecord.iata.org/ns/cargo#specimenTypeCode';

    /** Stackable indicator for the pieces (boolean) */
    public const string stackable = 'https://onerecord.iata.org/ns/cargo#stackable';

    /** Reference to the station (Airport), mandatory */
    public const string station = 'https://onerecord.iata.org/ns/cargo#station';

    /** Remarks related to specific stations in the routing (e.g. Embargo in XXX) */
    public const string stationRemarks = 'https://onerecord.iata.org/ns/cargo#stationRemarks';

    /** Status of the Booking Option */
    public const string statusBookingOption = 'https://onerecord.iata.org/ns/cargo#statusBookingOption';

    /** Short text stating the exact place of storage */
    public const string storagePlaceIdentifier = 'https://onerecord.iata.org/ns/cargo#storagePlaceIdentifier';

    /** Reference to the Objects being stored in or stored out */
    public const string storedObjects = 'https://onerecord.iata.org/ns/cargo#storedObjects';

    /** References to all StoringActions performed for the Storing Activity */
    public const string storingActions = 'https://onerecord.iata.org/ns/cargo#storingActions';

    /** Short text holding the process number if necessary */
    public const string storingIdentifier = 'https://onerecord.iata.org/ns/cargo#storingIdentifier';

    /** Enum stating whether the StoringAction describes the store-in or the store-out */
    public const string storingType = 'https://onerecord.iata.org/ns/cargo#storingType';

    /** Street address including street name, street number, building number, apartment etc */
    public const string streetAddressLines = 'https://onerecord.iata.org/ns/cargo#streetAddressLines';

    /** Reference to the Location this is a Sublocation of */
    public const string subLocationOf = 'https://onerecord.iata.org/ns/cargo#subLocationOf';

    /** References to Sublocations that describe the Location in more detail */
    public const string subLocations = 'https://onerecord.iata.org/ns/cargo#subLocations';

    /** References to all sub-Organizations */
    public const string subOrganization = 'https://onerecord.iata.org/ns/cargo#subOrganization';

    /** Subtotal of the charge */
    public const string subTotal = 'https://onerecord.iata.org/ns/cargo#subTotal';

    /**
     * Information Identifier. Code identifying a piece of information/entity e.g. "IMP" for import, "EXP"
     * for export, "AGT" for Agent, "ISS" for The Regulated Agent Issuing the Security Status for a
     * Consignment etc. Condition: At least one of the three elements (Country Code, Information Identifier
     * or Customs, Security and Regulatory Control Information Identifier) must be completed
     */
    public const string subjectCode = 'https://onerecord.iata.org/ns/cargo#subjectCode';

    /**
     * Additional information that may be added in addition to the proper shipping name to more fully
     * describe the goods or to identify a particular condition
     */
    public const string supplementaryInfoPrefix = 'https://onerecord.iata.org/ns/cargo#supplementaryInfoPrefix';

    /**
     * Additional information that may be added in addition to the proper shipping to more fully describe
     * the goods or to identify a particular condition
     */
    public const string supplementaryInfoSuffix = 'https://onerecord.iata.org/ns/cargo#supplementaryInfoSuffix';

    /** Tare weight of the empty ULD */
    public const string tareWeight = 'https://onerecord.iata.org/ns/cargo#tareWeight';

    /** Item target country. Refer ISO 3166-2 */
    public const string targetCountry = 'https://onerecord.iata.org/ns/cargo#targetCountry';

    /** Information about taxes */
    public const string taxAmount = 'https://onerecord.iata.org/ns/cargo#taxAmount';

    /** Tax due Agent (VAT/GST on Commission). Total VAT/TAX amount payable by airline to agent */
    public const string taxDueAgent = 'https://onerecord.iata.org/ns/cargo#taxDueAgent';

    /**
     * Tax due Airline (as per AWB, or VAT/GST as per invoice). Total VAT/TAX amount payable by agent to
     * airline
     */
    public const string taxDueAirline = 'https://onerecord.iata.org/ns/cargo#taxDueAirline';

    /**
     * This is additional chemical name(s) required for some proper shipping names. When added the
     * technical must be shown in parentheses immediately following the proper shipping name.
     */
    public const string technicalName = 'https://onerecord.iata.org/ns/cargo#technicalName';

    /** Temperature instructions if a specific range */
    public const string temperatureInstructions = 'https://onerecord.iata.org/ns/cargo#temperatureInstructions';

    /** Preferred unit for temperature */
    public const string temperatureUnit = 'https://onerecord.iata.org/ns/cargo#temperatureUnit';

    /** Purpose of the template */
    public const string templatePurpose = 'https://onerecord.iata.org/ns/cargo#templatePurpose';

    /** Text for the Answer */
    public const string text = 'https://onerecord.iata.org/ns/cargo#text';

    /** Strings to provide free text handling instructions such as SSR and OSI */
    public const string textualHandlingInstructions = 'https://onerecord.iata.org/ns/cargo#textualHandlingInstructions';

    /** Postal / ZIP code */
    public const string textualPostCode = 'https://onerecord.iata.org/ns/cargo#textualPostCode';

    /** Textual value filled on use context (eg. characteristic colour, contactDetail mail address, etc.) */
    public const string textualValue = 'https://onerecord.iata.org/ns/cargo#textualValue';

    /** Time of availability of the shipment as per CargoIQ definition */
    public const string timeOfAvailability = 'https://onerecord.iata.org/ns/cargo#timeOfAvailability';

    /** Schedule preferences of the request */
    public const string timePreferences = 'https://onerecord.iata.org/ns/cargo#timePreferences';

    /**
     * Dimensions of the whole shipment
     *
     * @deprecated since 3.3
     */
    public const string totalDimensions = 'https://onerecord.iata.org/ns/cargo#totalDimensions';

    /** Total gross weight of the whole shipment */
    public const string totalGrossWeight = 'https://onerecord.iata.org/ns/cargo#totalGrossWeight';

    /** Total transit time as per CargoIQ definition, expressed as a duration */
    public const string totalTransitTime = 'https://onerecord.iata.org/ns/cargo#totalTransitTime';

    /** Total volume fo the volume piece group */
    public const string totalVolume = 'https://onerecord.iata.org/ns/cargo#totalVolume';

    /**
     * Volumetric weight of the whole shipment
     *
     * @deprecated since 3.2
     */
    public const string totalVolumetricWeight = 'https://onerecord.iata.org/ns/cargo#totalVolumetricWeight';

    /** Purpose of the transaction in free text (box 5a) */
    public const string transactionPurpose = 'https://onerecord.iata.org/ns/cargo#transactionPurpose';

    /** Code indicating the purpose of the transaction (box 5a) */
    public const string transactionPurposeCode = 'https://onerecord.iata.org/ns/cargo#transactionPurposeCode';

    /**
     * Reference to the organization the shipment was transferred from in a status update event
     *
     * @since 3.3
     */
    public const string transferredFrom = 'https://onerecord.iata.org/ns/cargo#transferredFrom';

    /**
     * Reference to the organization the shipment was transferred to in a status update event
     *
     * @since 3.3
     */
    public const string transferredTo = 'https://onerecord.iata.org/ns/cargo#transferredTo';

    /** Reference to the Air Waybill or other transport contract document (box 15) */
    public const string transportContractId = 'https://onerecord.iata.org/ns/cargo#transportContractId';

    /** Code specifying the transport document name (box 15) */
    public const string transportContractTypeCode = 'https://onerecord.iata.org/ns/cargo#transportContractTypeCode';

    /** Airline flight number, or rail/truck/maritime line id */
    public const string transportIdentifier = 'https://onerecord.iata.org/ns/cargo#transportIdentifier';

    /**
     * Radioactive Transport-Index value of the package or all packed in one. Conditionally mandatory and
     * applies to categories II-Yellow and III-Yellow only; field only contains the value, if printed, TI
     * must be added as a prefix to the value to be printed in the Packing Instructions column
     */
    public const string transportIndexNumeric = 'https://onerecord.iata.org/ns/cargo#transportIndexNumeric';

    /** Reference to the Transport Legs of the proposed routing */
    public const string transportLegs = 'https://onerecord.iata.org/ns/cargo#transportLegs';

    /** Type of transport means service, e.g. Aircraftor Truck */
    public const string transportMeansServiceType = 'https://onerecord.iata.org/ns/cargo#transportMeansServiceType';

    /** Type of transport means, e.g. 744, RFS */
    public const string transportMeansType = 'https://onerecord.iata.org/ns/cargo#transportMeansType';

    /**
     * Reference to the transport movement a status update event refers to
     *
     * @since 3.3
     */
    public const string transportMovementReference = 'https://onerecord.iata.org/ns/cargo#transportMovementReference';

    /** Company operating the transport means */
    public const string transportOrganization = 'https://onerecord.iata.org/ns/cargo#transportOrganization';

    /** Turnable indicator for the pieces (boolean) */
    public const string turnable = 'https://onerecord.iata.org/ns/cargo#turnable';

    /**
     * Packaging type identifier as per UNECE Rec 21 Annex V and VI e.g. 1A - Drum, steel - Packaging
     * material code. Identifies the Logistic Unit package type. UN Recommendation on Transport of
     * Dangerous Goods, Model Regulations
     */
    public const string typeCode = 'https://onerecord.iata.org/ns/cargo#typeCode';

    /** Required for some CO2 calculations */
    public const string typicalCo2Coefficient = 'https://onerecord.iata.org/ns/cargo#typicalCo2Coefficient';

    /** Typical fuel consumption (e.g. 2 L / 1 nm) */
    public const string typicalFuelConsumption = 'https://onerecord.iata.org/ns/cargo#typicalFuelConsumption';

    /** Contour code as per IATA ULD Regulation */
    public const string uldContourCode = 'https://onerecord.iata.org/ns/cargo#uldContourCode';

    /** Indicator related to ULD loading (e.g. Main deck only) */
    public const string uldLoadingIndicator = 'https://onerecord.iata.org/ns/cargo#uldLoadingIndicator';

    /**
     * Information about the ULD owner code described in a ULD specific piece or used for a rate in a Line
     * Item
     */
    public const string uldOwnerCode = 'https://onerecord.iata.org/ns/cargo#uldOwnerCode';

    /** ULD Rate information - ULD Rate Class Type */
    public const string uldRateClassType = 'https://onerecord.iata.org/ns/cargo#uldRateClassType';

    /** References to ULDs for which the rate applies */
    public const string uldReferences = 'https://onerecord.iata.org/ns/cargo#uldReferences';

    /** Serial number that allows to uniquely identify the ULD */
    public const string uldSerialNumber = 'https://onerecord.iata.org/ns/cargo#uldSerialNumber';

    /**
     * Information about the ULD tare weight used for the rate described by the Line Item
     *
     * @deprecated since 3.2
     */
    public const string uldTareWeightForRate = 'https://onerecord.iata.org/ns/cargo#uldTareWeightForRate';

    /** Type of ULD as per IATA ULD Regulation */
    public const string uldType = 'https://onerecord.iata.org/ns/cargo#uldType';

    /**
     * Standard Unit Load Device type code e.g. AKE - Certified Container - Contoured. Refer to IATA ULD
     * Technical Manual
     */
    public const string uldTypeCode = 'https://onerecord.iata.org/ns/cargo#uldTypeCode';

    /**
     * Reference identifying the United Nations Dangerous Goods serial number assigned within the UN to
     * substances and articles contained in a list of the dangerous goods most commonly carried. e.g. 1189
     * - Ethylene glycol monomethyl ether acetate
     */
    public const string unNumber = 'https://onerecord.iata.org/ns/cargo#unNumber';

    /** Manufacturer's unique product identifier */
    public const string uniqueIdentifier = 'https://onerecord.iata.org/ns/cargo#uniqueIdentifier';

    /**
     * Unit of measurement as per MeasurementUnitCode codelist. If the code is not present, create an
     * instance of MeasurementUnitCode based on UNECE Rec. 20 Rev. 17e-2021
     */
    public const string unit = 'https://onerecord.iata.org/ns/cargo#unit';

    /** Specific commodity code linked to commodity */
    public const string unitBasis = 'https://onerecord.iata.org/ns/cargo#unitBasis';

    /** Product price per unit in the base */
    public const string unitPrice = 'https://onerecord.iata.org/ns/cargo#unitPrice';

    /** Reference to unit preferences of the request (e.g. kg or cm) */
    public const string unitsPreference = 'https://onerecord.iata.org/ns/cargo#unitsPreference';

    /** References to BookingOptionRequests that request to update the Booking */
    public const string updateBookingOptionRequests = 'https://onerecord.iata.org/ns/cargo#updateBookingOptionRequests';

    /** Unique Piece Identifier (UPID) of the piece. Refer IATA Recommended Practice 1689 */
    public const string upid = 'https://onerecord.iata.org/ns/cargo#upid';

    /** Reference to the Check the template was used in */
    public const string usedInCheck = 'https://onerecord.iata.org/ns/cargo#usedInCheck';

    /** Reference to the Template used in the Check */
    public const string usedTemplate = 'https://onerecord.iata.org/ns/cargo#usedTemplate';

    /**
     * total number of specimens exported in the current calendar year and the current annual quota for the
     * species concerned (box 11a)
     */
    public const string usedToDateQuotaQuantity = 'https://onerecord.iata.org/ns/cargo#usedToDateQuotaQuantity';

    /** Validity start date based on usage context */
    public const string validFrom = 'https://onerecord.iata.org/ns/cargo#validFrom';

    /** Validity end date (date of expiry) based on usage context */
    public const string validUntil = 'https://onerecord.iata.org/ns/cargo#validUntil';

    /**
     * Information about the valuation charge (AWB box 25A / 25B)
     *
     * @since 3.3
     */
    public const string valuationCharge = 'https://onerecord.iata.org/ns/cargo#valuationCharge';

    /** Indicate if subject to VAT (boolean) */
    public const string vatIndicator = 'https://onerecord.iata.org/ns/cargo#vatIndicator';

    /** Model or make of the vehicle (e.g. A33-3) */
    public const string vehicleModel = 'https://onerecord.iata.org/ns/cargo#vehicleModel';

    /** Vehicle identification - e.g. aircraft registration number */
    public const string vehicleRegistration = 'https://onerecord.iata.org/ns/cargo#vehicleRegistration';

    /** Size of the vehicle - free text */
    public const string vehicleSize = 'https://onerecord.iata.org/ns/cargo#vehicleSize';

    /**
     * Vehicle or container type. Refer UNECE28, e.g. 4.. - Aircraft, type unknown.For Air refer to IATA
     * Standard Schedules Information Manual in section ATA/IATA Aircraft Types
     */
    public const string vehicleType = 'https://onerecord.iata.org/ns/cargo#vehicleType';

    /** Version of the template */
    public const string version = 'https://onerecord.iata.org/ns/cargo#version';

    /** Volume */
    public const string volume = 'https://onerecord.iata.org/ns/cargo#volume';

    /** Preferred unit for volume */
    public const string volumeUnit = 'https://onerecord.iata.org/ns/cargo#volumeUnit';

    /**
     * Volumetric weight details
     *
     * @deprecated since 3.2
     */
    public const string volumetricWeight = 'https://onerecord.iata.org/ns/cargo#volumetricWeight';

    /**
     * Volumetric weight used for the rate described by this line item
     *
     * @deprecated since 3.2
     */
    public const string volumetricWeightForRate = 'https://onerecord.iata.org/ns/cargo#volumetricWeightForRate';

    /** Reference to the Waybill of the shipment */
    public const string waybill = 'https://onerecord.iata.org/ns/cargo#waybill';

    /** Information about rates applying to this Waybill handled as line item */
    public const string waybillLineItems = 'https://onerecord.iata.org/ns/cargo#waybillLineItems';

    /** House or Master Waybill unique identifier */
    public const string waybillNumber = 'https://onerecord.iata.org/ns/cargo#waybillNumber';

    /** Prefix used for the Waybill Number. Refer to IATA Airlines Codes */
    public const string waybillPrefix = 'https://onerecord.iata.org/ns/cargo#waybillPrefix';

    /** Type of the Waybill: House, Direct or Master */
    public const string waybillType = 'https://onerecord.iata.org/ns/cargo#waybillType';

    /** Weight of the item */
    public const string weight = 'https://onerecord.iata.org/ns/cargo#weight';

    /** Preferred unit for weight */
    public const string weightUnit = 'https://onerecord.iata.org/ns/cargo#weightUnit';

    /**
     * Indicator whether the payment for the Weight/Valuation is to be made at origin (prepaid) or at
     * destination (collect) as per bullet point 13 - data element 14a/14b from AWB
     */
    public const string weightValuationIndicator = 'https://onerecord.iata.org/ns/cargo#weightValuationIndicator';

    /** Width */
    public const string width = 'https://onerecord.iata.org/ns/cargo#width';


    // Named individuals

    /** Indicates the sensor type as accelerometer */
    public const string ACCELEROMETER = 'https://onerecord.iata.org/ns/cargo#ACCELEROMETER';

    /** Used when a LogisticsActivity is active */
    public const string ACTIVE = 'https://onerecord.iata.org/ns/cargo#ACTIVE';

    /** Used when a time is actual */
    public const string ACTUAL = 'https://onerecord.iata.org/ns/cargo#ACTUAL';

    /** Indicates a contact detail as alternate email address */
    public const string ALTERNATE_EMAIL_ADDRESS = 'https://onerecord.iata.org/ns/cargo#ALTERNATE_EMAIL_ADDRESS';

    /** Indicates a contact detail as alternate phone number */
    public const string ALTERNATE_PHONE_NUMBER = 'https://onerecord.iata.org/ns/cargo#ALTERNATE_PHONE_NUMBER';

    /** Used when a booking option (or proposal) is bookable */
    public const string BOOKABLE = 'https://onerecord.iata.org/ns/cargo#BOOKABLE';

    /** Used when a booking option proposal is booked */
    public const string BOOKED = 'https://onerecord.iata.org/ns/cargo#BOOKED';

    /** Indicates the load type as bulk */
    public const string BULK = 'https://onerecord.iata.org/ns/cargo#BULK';

    /** Used when a LogisticsActivity is cancelled */
    public const string CANCELLED = 'https://onerecord.iata.org/ns/cargo#CANCELLED';

    /** Used when a LogisticsActivity is complete */
    public const string COMPLETE = 'https://onerecord.iata.org/ns/cargo#COMPLETE';

    /** Describes a composition, for example the loading of a container or the build-up of an ULD */
    public const string COMPOSITION = 'https://onerecord.iata.org/ns/cargo#COMPOSITION';

    /** Used when a booking is confirmed */
    public const string CONFIRMED = 'https://onerecord.iata.org/ns/cargo#CONFIRMED';

    /** Consignee's Account */
    public const string CONSIGNEE = 'https://onerecord.iata.org/ns/cargo#CONSIGNEE';

    /** Consignor */
    public const string CONSIGNOR = 'https://onerecord.iata.org/ns/cargo#CONSIGNOR';

    /** Indicates a contact person as customer contact */
    public const string CUSTOMER_CONTACT = 'https://onerecord.iata.org/ns/cargo#CUSTOMER_CONTACT';

    /** Indicates a contact person as customs contact */
    public const string CUSTOMS_CONTACT = 'https://onerecord.iata.org/ns/cargo#CUSTOMS_CONTACT';

    /** Describes a decomposition, for example the unloading of a container or the break-down of an ULD */
    public const string DECOMPOSITION = 'https://onerecord.iata.org/ns/cargo#DECOMPOSITION';

    /** Used when a booking is deleted */
    public const string DELETED = 'https://onerecord.iata.org/ns/cargo#DELETED';

    /** Indicates a Direct waybill */
    public const string DIRECT = 'https://onerecord.iata.org/ns/cargo#DIRECT';

    /** Indicates a contact detail as email address */
    public const string EMAIL_ADDRESS = 'https://onerecord.iata.org/ns/cargo#EMAIL_ADDRESS';

    /** Indicates a contact person as emergency contact */
    public const string EMERGENCY_CONTACT = 'https://onerecord.iata.org/ns/cargo#EMERGENCY_CONTACT';

    /** Used when a time is estimated */
    public const string ESTIMATED = 'https://onerecord.iata.org/ns/cargo#ESTIMATED';

    /** Used when a time is expected */
    public const string EXPECTED = 'https://onerecord.iata.org/ns/cargo#EXPECTED';

    /** Used when a booking option proposal is expired */
    public const string EXPIRED = 'https://onerecord.iata.org/ns/cargo#EXPIRED';

    /** Indicates a contact detail as fax number */
    public const string FAX_NUMBER = 'https://onerecord.iata.org/ns/cargo#FAX_NUMBER';

    /** Freight Forwarder Account */
    public const string FF = 'https://onerecord.iata.org/ns/cargo#FF';

    /** Indicates the sensor type as geolocation */
    public const string GEOLOCATION = 'https://onerecord.iata.org/ns/cargo#GEOLOCATION';

    /** Indicates a House Waybill */
    public const string HOUSE = 'https://onerecord.iata.org/ns/cargo#HOUSE';

    /** Indicates the sensor type as humidity */
    public const string HUMIDITY = 'https://onerecord.iata.org/ns/cargo#HUMIDITY';

    /** Indicates the described direction in a movement time as inbound */
    public const string INBOUND = 'https://onerecord.iata.org/ns/cargo#INBOUND';

    /** Indicates the sensor type as light */
    public const string LIGHT = 'https://onerecord.iata.org/ns/cargo#LIGHT';

    /** Describes a loading process, for example putting an ULD on an aircraft or a piece in a truck */
    public const string LOADING = 'https://onerecord.iata.org/ns/cargo#LOADING';

    /** Indicates the load type as loose */
    public const string LOOSE = 'https://onerecord.iata.org/ns/cargo#LOOSE';

    /** Indicates the mode qualifier as main carriage */
    public const string MAIN_CARRIAGE = 'https://onerecord.iata.org/ns/cargo#MAIN_CARRIAGE';

    /** Indicates a Master Waybill */
    public const string MASTER = 'https://onerecord.iata.org/ns/cargo#MASTER';

    /** Used when a booking option is nonbookable */
    public const string NONBOOKABLE = 'https://onerecord.iata.org/ns/cargo#NONBOOKABLE';

    /** Used when a booking option proposal is not bookable */
    public const string NOT_BOOKABLE = 'https://onerecord.iata.org/ns/cargo#NOT_BOOKABLE';

    /** Indicates the mode qualifier as on carriage */
    public const string ON_CARRIAGE = 'https://onerecord.iata.org/ns/cargo#ON_CARRIAGE';

    /** Used when a booking option proposal is on request */
    public const string ON_REQUEST = 'https://onerecord.iata.org/ns/cargo#ON_REQUEST';

    /** Indicates the described direction in a movement time as outbound */
    public const string OUTBOUND = 'https://onerecord.iata.org/ns/cargo#OUTBOUND';

    /** Indicates the load type as pallet */
    public const string PALLET = 'https://onerecord.iata.org/ns/cargo#PALLET';

    /** Used when a LogisticsActivity is pending */
    public const string PENDING = 'https://onerecord.iata.org/ns/cargo#PENDING';

    /** Indicates a contact detail as phone number */
    public const string PHONE_NUMBER = 'https://onerecord.iata.org/ns/cargo#PHONE_NUMBER';

    /** Used when a time is planned */
    public const string PLANNED = 'https://onerecord.iata.org/ns/cargo#PLANNED';

    /** Indicates the sensor type as pressure */
    public const string PRESSURE = 'https://onerecord.iata.org/ns/cargo#PRESSURE';

    /** Indicates the mode qualifier as pre carriage */
    public const string PRE_CARRIAGE = 'https://onerecord.iata.org/ns/cargo#PRE_CARRIAGE';

    /** Used when a booking or booking option is queued or pending */
    public const string QUEUED = 'https://onerecord.iata.org/ns/cargo#QUEUED';

    /** Used when a booking is rejected */
    public const string REJECTED = 'https://onerecord.iata.org/ns/cargo#REJECTED';

    /** Used when a time is requested */
    public const string REQUESTED = 'https://onerecord.iata.org/ns/cargo#REQUESTED';

    /** Used when a time is scheduled */
    public const string SCHEDULED = 'https://onerecord.iata.org/ns/cargo#SCHEDULED';

    /** Describes a store-in process, where a physical object is assigned to a specific location */
    public const string STORE_IN = 'https://onerecord.iata.org/ns/cargo#STORE_IN';

    /** Describes a store-out process, where a physical object leaves a specific location */
    public const string STORE_OUT = 'https://onerecord.iata.org/ns/cargo#STORE_OUT';

    /** Indicates a contact detail as telex */
    public const string TELEX = 'https://onerecord.iata.org/ns/cargo#TELEX';

    /** Indicates the sensor type as thermometer */
    public const string THERMOMETER = 'https://onerecord.iata.org/ns/cargo#THERMOMETER';

    /** Indicates the sensor type as tilt */
    public const string TILT = 'https://onerecord.iata.org/ns/cargo#TILT';

    /** Indicates the load type as uld */
    public const string UNIT_LOAD_DEVICE = 'https://onerecord.iata.org/ns/cargo#UNIT_LOAD_DEVICE';

    /** Describes an unloading process, for example removing an ULD from an aircraft or a piece from a truck */
    public const string UNLOADING = 'https://onerecord.iata.org/ns/cargo#UNLOADING';

    /** Indicates the that the movement time describes an unplanned stop */
    public const string UNPLANNED_STOP = 'https://onerecord.iata.org/ns/cargo#UNPLANNED_STOP';

    /** Indicates the sensor type as vibration */
    public const string VIBRATION = 'https://onerecord.iata.org/ns/cargo#VIBRATION';

    /** Indicates a contact detail as website */
    public const string WEBSITE = 'https://onerecord.iata.org/ns/cargo#WEBSITE';
}
