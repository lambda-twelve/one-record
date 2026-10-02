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
final class CargoSchema
{
    public const string NAMESPACE = 'https://onerecord.iata.org/ns/cargo#';

    /** @var list<string> versions merged into this schema, oldest first */
    public const array VERSIONS = [
        '3.2',
        '3.3',
    ];

    /**
     * Keyed by class IRI. `properties` maps the property IRIs this class restricts (its own, not inherited) to the version that attached them.
     *
     * @var array<string, array{name: string, parents: list<string>, properties: array<string, string>, since: string, deprecatedIn: ?string, removedIn: ?string}>
     */
    public const array CLASSES = [
        'https://onerecord.iata.org/ns/cargo#AccountNumber' => [
            'name' => 'AccountNumber',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#accountNumberType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#textualValue' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#AccountType' => [
            'name' => 'AccountType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#AccountingNote' => [
            'name' => 'AccountingNote',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#accountingNoteIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#accountingNoteText' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ActionTimeType' => [
            'name' => 'ActionTimeType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ActivitySequence' => [
            'name' => 'ActivitySequence',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#activity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#sequenceNumber' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Actor' => [
            'name' => 'Actor',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAgent',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#associatedOrganization' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Address' => [
            'name' => 'Address',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#addressCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#cityCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#cityName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#country' => '3.2',
                'https://onerecord.iata.org/ns/cargo#postOfficeBox' => '3.2',
                'https://onerecord.iata.org/ns/cargo#postalCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#regionCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#streetAddressLines' => '3.2',
                'https://onerecord.iata.org/ns/cargo#textualPostCode' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Adjustments' => [
            'name' => 'Adjustments',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#correctionNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#correctionSerialNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#reasonsForAdjustments' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Answer' => [
            'name' => 'Answer',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#answerActor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#answerValue' => '3.2',
                'https://onerecord.iata.org/ns/cargo#givenAtLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#question' => '3.2',
                'https://onerecord.iata.org/ns/cargo#text' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BillingDetails' => [
            'name' => 'BillingDetails',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#adjustments' => '3.2',
                'https://onerecord.iata.org/ns/cargo#awbAcceptanceDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#awbDeliveryDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#awbExecutionDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#awbUseIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#commission' => '3.2',
                'https://onerecord.iata.org/ns/cargo#commissionIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#commissionPercentage' => '3.2',
                'https://onerecord.iata.org/ns/cargo#detailedWaybill' => '3.2',
                'https://onerecord.iata.org/ns/cargo#discount' => '3.2',
                'https://onerecord.iata.org/ns/cargo#exchangeRate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#nbCorrections' => '3.2',
                'https://onerecord.iata.org/ns/cargo#taxDueAgent' => '3.2',
                'https://onerecord.iata.org/ns/cargo#taxDueAirline' => '3.2',
                'https://onerecord.iata.org/ns/cargo#vatIndicator' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Booking' => [
            'name' => 'Booking',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsService',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#additionalInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#arrivalLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingRequest' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingSegments' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingShipmentDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingStatus' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingTimes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrierProduct' => '3.2',
                'https://onerecord.iata.org/ns/cargo#departureLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#issuedForWaybill' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shippingInfo' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shippingRefNo' => '3.2',
                'https://onerecord.iata.org/ns/cargo#stationRemarks' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportLegs' => '3.2',
                'https://onerecord.iata.org/ns/cargo#updateBookingOptionRequests' => '3.2',
                'https://onerecord.iata.org/ns/cargo#waybillNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#waybillPrefix' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingOption' => [
            'name' => 'BookingOption',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#additionalInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#alternatives' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingTimes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrierProduct' => '3.2',
                'https://onerecord.iata.org/ns/cargo#forBookingOptionRequest' => '3.2',
                'https://onerecord.iata.org/ns/cargo#forBookingRequest' => '3.2',
                'https://onerecord.iata.org/ns/cargo#offerValidFrom' => '3.2',
                'https://onerecord.iata.org/ns/cargo#offerValidTo' => '3.2',
                'https://onerecord.iata.org/ns/cargo#price' => '3.2',
                'https://onerecord.iata.org/ns/cargo#requestMatch' => '3.2',
                'https://onerecord.iata.org/ns/cargo#stationRemarks' => '3.2',
                'https://onerecord.iata.org/ns/cargo#statusBookingOption' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportLegs' => '3.2',
                'https://onerecord.iata.org/ns/cargo#unitsPreference' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingOptionRequest' => [
            'name' => 'BookingOptionRequest',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#bookingOptions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingPreference' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingShipmentDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#bookingToUpdate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrierProduct' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#knownShipper' => '3.2',
                'https://onerecord.iata.org/ns/cargo#timePreferences' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportLegs' => '3.2',
                'https://onerecord.iata.org/ns/cargo#unitsPreference' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingOptionStatus' => [
            'name' => 'BookingOptionStatus',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingPreferences' => [
            'name' => 'BookingPreferences',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#aircraftPossibilityCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#excludedViaPoints' => '3.2',
                'https://onerecord.iata.org/ns/cargo#includedViaPoints' => '3.2',
                'https://onerecord.iata.org/ns/cargo#maxSegments' => '3.2',
                'https://onerecord.iata.org/ns/cargo#preferredTransportId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#priceReferenceId' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingRequest' => [
            'name' => 'BookingRequest',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#booking' => '3.2',
                'https://onerecord.iata.org/ns/cargo#forBookingOption' => '3.2',
                'https://onerecord.iata.org/ns/cargo#waybillNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#waybillPrefix' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingSegment' => [
            'name' => 'BookingSegment',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#allotmentCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceGroups' => '3.2',
                'https://onerecord.iata.org/ns/cargo#spaceAllocationCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportLegs' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingShipment' => [
            'name' => 'BookingShipment',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#chargeableWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#consolidationIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#customsInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#densityGroupCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#expectedCommodity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#expectedHScode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#forBookingOptionRequest' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceGroups' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specialHandlingCodes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specialServiceRequests' => '3.2',
                'https://onerecord.iata.org/ns/cargo#temperatureInstructions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#textualHandlingInstructions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#totalDimensions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#totalGrossWeight' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingStatus' => [
            'name' => 'BookingStatus',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BookingTimes' => [
            'name' => 'BookingTimes',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#earliestAcceptanceTime' => '3.2',
                'https://onerecord.iata.org/ns/cargo#latestAcceptanceTime' => '3.2',
                'https://onerecord.iata.org/ns/cargo#latestArrivalTime' => '3.2',
                'https://onerecord.iata.org/ns/cargo#timeOfAvailability' => '3.2',
                'https://onerecord.iata.org/ns/cargo#totalTransitTime' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CO2Emissions' => [
            'name' => 'CO2Emissions',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#calculatedEmissions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#calculationFor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#methodName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#methodVersion' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Carrier' => [
            'name' => 'Carrier',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Company',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#airlineCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#prefix' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CarrierProduct' => [
            'name' => 'CarrierProduct',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#productCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#productDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serviceLevelCode' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Characteristic' => [
            'name' => 'Characteristic',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#characteristicType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#textualValue' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Check' => [
            'name' => 'Check',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#checkTotalResult' => '3.2',
                'https://onerecord.iata.org/ns/cargo#checkedObject' => '3.2',
                'https://onerecord.iata.org/ns/cargo#checker' => '3.2',
                'https://onerecord.iata.org/ns/cargo#usedTemplate' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CheckTemplate' => [
            'name' => 'CheckTemplate',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#date' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#legacyTemplate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#name' => '3.2',
                'https://onerecord.iata.org/ns/cargo#questions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#templatePurpose' => '3.2',
                'https://onerecord.iata.org/ns/cargo#usedInCheck' => '3.2',
                'https://onerecord.iata.org/ns/cargo#version' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CheckTotalResult' => [
            'name' => 'CheckTotalResult',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#certifiedByActor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#checkRemark' => '3.2',
                'https://onerecord.iata.org/ns/cargo#passed' => '3.2',
                'https://onerecord.iata.org/ns/cargo#resultOfCheck' => '3.2',
                'https://onerecord.iata.org/ns/cargo#resultValue' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CodeListElement' => [
            'name' => 'CodeListElement',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#code' => '3.2',
                'https://onerecord.iata.org/ns/cargo#codeDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#codeLevel' => '3.2',
                'https://onerecord.iata.org/ns/cargo#codeListName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#codeListReference' => '3.2',
                'https://onerecord.iata.org/ns/cargo#codeListVersion' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Company' => [
            'name' => 'Company',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#iataCargoAgentCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#iataCargoAgentLocationIdentifier' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Composing' => [
            'name' => 'Composing',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#composedMaterials' => '3.2',
                'https://onerecord.iata.org/ns/cargo#composedPieces' => '3.2',
                'https://onerecord.iata.org/ns/cargo#compositionType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadingUnit' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CompositionType' => [
            'name' => 'CompositionType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ContactDetail' => [
            'name' => 'ContactDetail',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#contactDetailType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#textualValue' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ContactDetailType' => [
            'name' => 'ContactDetailType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ContactRole' => [
            'name' => 'ContactRole',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CurrencyValue' => [
            'name' => 'CurrencyValue',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#currencyUnit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#numericalValue' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CustomsInformation' => [
            'name' => 'CustomsInformation',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#contentCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#country' => '3.2',
                'https://onerecord.iata.org/ns/cargo#issuedForPiece' => '3.2',
                'https://onerecord.iata.org/ns/cargo#issuedForShipment' => '3.2',
                'https://onerecord.iata.org/ns/cargo#note' => '3.2',
                'https://onerecord.iata.org/ns/cargo#ociLineNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherCustomsInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#subjectCode' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#DgDeclaration' => [
            'name' => 'DgDeclaration',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#aircraftLimitationInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#arrivalLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#complianceDeclarationText' => '3.2',
                'https://onerecord.iata.org/ns/cargo#declarationDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#declarationPlace' => '3.2',
                'https://onerecord.iata.org/ns/cargo#departureLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#exclusiveUseIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#handlingInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#issuedForPiece' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shipperDeclarationText' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shippingRefNo' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#DgProductRadioactive' => [
            'name' => 'DgProductRadioactive',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#dgRaTypeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#fissileExceptionIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#fissileExceptionReference' => '3.2',
                'https://onerecord.iata.org/ns/cargo#forProductDg' => '3.2',
                'https://onerecord.iata.org/ns/cargo#isotopes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportIndexNumeric' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope' => [
            'name' => 'DgRadioactiveIsotope',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#activityLevelMeasure' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contentOfDgProductRadioactive' => '3.2',
                'https://onerecord.iata.org/ns/cargo#criticalitySafetyIndexNumeric' => '3.2',
                'https://onerecord.iata.org/ns/cargo#isotopeId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#isotopeName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#lowDispersibleIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#physicalChemicalForm' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specialFormIndicator' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Dimensions' => [
            'name' => 'Dimensions',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#height' => '3.2',
                'https://onerecord.iata.org/ns/cargo#length' => '3.2',
                'https://onerecord.iata.org/ns/cargo#volume' => '3.2',
                'https://onerecord.iata.org/ns/cargo#width' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#DirectionType' => [
            'name' => 'DirectionType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#EpermitConsignment' => [
            'name' => 'EpermitConsignment',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#consignmentItems' => '3.2',
                'https://onerecord.iata.org/ns/cargo#epermit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#examiningQuantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#usedToDateQuotaQuantity' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#EpermitSignature' => [
            'name' => 'EpermitSignature',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#forEpermit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#securityStampId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#signatoryCompany' => '3.2',
                'https://onerecord.iata.org/ns/cargo#signatoryRole' => '3.2',
                'https://onerecord.iata.org/ns/cargo#signatureDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#signatureStatement' => '3.2',
                'https://onerecord.iata.org/ns/cargo#signatureTypeCode' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#EventTimeType' => [
            'name' => 'EventTimeType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ExecutionStatus' => [
            'name' => 'ExecutionStatus',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ExternalReference' => [
            'name' => 'ExternalReference',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#checksum' => '3.2',
                'https://onerecord.iata.org/ns/cargo#createdAtLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#documentIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#documentLink' => '3.2',
                'https://onerecord.iata.org/ns/cargo#documentName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#documentType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#documentVersion' => '3.2',
                'https://onerecord.iata.org/ns/cargo#originator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#referenceForObjects' => '3.2',
                'https://onerecord.iata.org/ns/cargo#validFrom' => '3.2',
                'https://onerecord.iata.org/ns/cargo#validUntil' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Geolocation' => [
            'name' => 'Geolocation',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#elevation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#latitude' => '3.2',
                'https://onerecord.iata.org/ns/cargo#longitude' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#HandlingService' => [
            'name' => 'HandlingService',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsService',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#handlingServiceFor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serviceForWaybills' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serviceProvider' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serviceRequestor' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Insurance' => [
            'name' => 'Insurance',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#coveringOrganization' => '3.2',
                'https://onerecord.iata.org/ns/cargo#insuredAmount' => '3.2',
                'https://onerecord.iata.org/ns/cargo#insuredShipments' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#IotDevice' => [
            'name' => 'IotDevice',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#attachedToObject' => '3.2',
                'https://onerecord.iata.org/ns/cargo#connectedSensors' => '3.2',
                'https://onerecord.iata.org/ns/cargo#description' => '3.2',
                'https://onerecord.iata.org/ns/cargo#deviceModel' => '3.2',
                'https://onerecord.iata.org/ns/cargo#manufacturer' => '3.2',
                'https://onerecord.iata.org/ns/cargo#name' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serialNumber' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Item' => [
            'name' => 'Item',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#batchNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#dimensions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#expiryDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#inPiece' => '3.2',
                'https://onerecord.iata.org/ns/cargo#itemQuantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#lotNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#ofProduct' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#productionCountry' => '3.2',
                'https://onerecord.iata.org/ns/cargo#productionDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#quantityForUnitPrice' => '3.2',
                'https://onerecord.iata.org/ns/cargo#targetCountry' => '3.2',
                'https://onerecord.iata.org/ns/cargo#unitPrice' => '3.2',
                'https://onerecord.iata.org/ns/cargo#weight' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ItemDg' => [
            'name' => 'ItemDg',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#emergencyContact' => '3.2',
                'https://onerecord.iata.org/ns/cargo#netWeightMeasure' => '3.2',
                'https://onerecord.iata.org/ns/cargo#reportableQuantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#supplementaryInfoPrefix' => '3.2',
                'https://onerecord.iata.org/ns/cargo#supplementaryInfoSuffix' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LineItemPackage' => [
            'name' => 'LineItemPackage',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#packageGrossWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#packageSlac' => '3.2',
                'https://onerecord.iata.org/ns/cargo#packageVolume' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceReferences' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit' => [
            'name' => 'LiveAnimalsEpermit',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#consignee' => '3.2',
                'https://onerecord.iata.org/ns/cargo#consignments' => '3.2',
                'https://onerecord.iata.org/ns/cargo#copyIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#epermitNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#permitTypeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#permitTypeOtherDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#signatures' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specialConditions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transactionPurpose' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transactionPurposeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportContractId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportContractTypeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#validFrom' => '3.2',
                'https://onerecord.iata.org/ns/cargo#validUntil' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LoadType' => [
            'name' => 'LoadType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Loading' => [
            'name' => 'Loading',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#loadedMaterials' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadedPieces' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadedUnits' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadingPositionIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadingType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#onTransportMeans' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LoadingMaterial' => [
            'name' => 'LoadingMaterial',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#description' => '3.2',
                'https://onerecord.iata.org/ns/cargo#manufacturer' => '3.2',
                'https://onerecord.iata.org/ns/cargo#materialModel' => '3.2',
                'https://onerecord.iata.org/ns/cargo#materialType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serialNumber' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LoadingType' => [
            'name' => 'LoadingType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LoadingUnit' => [
            'name' => 'LoadingUnit',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#inUnitComposition' => '3.2',
                'https://onerecord.iata.org/ns/cargo#remarks' => '3.2',
                'https://onerecord.iata.org/ns/cargo#tareWeight' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Location' => [
            'name' => 'Location',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#address' => '3.2',
                'https://onerecord.iata.org/ns/cargo#geolocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#locationCodes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#locationName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#locationType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#onsiteActions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#subLocationOf' => '3.2',
                'https://onerecord.iata.org/ns/cargo#subLocations' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LogisticsAction' => [
            'name' => 'LogisticsAction',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#actionEndTime' => '3.2',
                'https://onerecord.iata.org/ns/cargo#actionStartTime' => '3.2',
                'https://onerecord.iata.org/ns/cargo#actionTimeType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactPersons' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#performedAt' => '3.2',
                'https://onerecord.iata.org/ns/cargo#servedActivity' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LogisticsActivity' => [
            'name' => 'LogisticsActivity',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#checkActions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactPersons' => '3.2',
                'https://onerecord.iata.org/ns/cargo#executionStatus' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.3',
                'https://onerecord.iata.org/ns/cargo#servedServices' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LogisticsAgent' => [
            'name' => 'LogisticsAgent',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LogisticsEvent' => [
            'name' => 'LogisticsEvent',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#creationDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#eventCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#eventDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#eventFor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#eventLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#eventName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#eventTimeType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#externalReferences' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#partialEventIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#recordingActor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#recordingOrganization' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LogisticsObject' => [
            'name' => 'LogisticsObject',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#checks' => '3.2',
                'https://onerecord.iata.org/ns/cargo#events' => '3.2',
                'https://onerecord.iata.org/ns/cargo#externalReferences' => '3.2',
                'https://onerecord.iata.org/ns/cargo#skeletonIndicator' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LogisticsService' => [
            'name' => 'LogisticsService',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#activitySequences' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactPersons' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LoosePiece' => [
            'name' => 'LoosePiece',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#pieceHeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceLength' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceWidth' => '3.2',
                'https://onerecord.iata.org/ns/cargo#stackable' => '3.2',
                'https://onerecord.iata.org/ns/cargo#totalVolume' => '3.2',
                'https://onerecord.iata.org/ns/cargo#turnable' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Measurement' => [
            'name' => 'Measurement',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#measurementTimestamp' => '3.2',
                'https://onerecord.iata.org/ns/cargo#measurementValue' => '3.2',
                'https://onerecord.iata.org/ns/cargo#recordedGeolocation' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ModeQualifier' => [
            'name' => 'ModeQualifier',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#MovementTime' => [
            'name' => 'MovementTime',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#direction' => '3.2',
                'https://onerecord.iata.org/ns/cargo#movementMilestone' => '3.2',
                'https://onerecord.iata.org/ns/cargo#movementTimeType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#movementTimestamp' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#MovementTimeType' => [
            'name' => 'MovementTimeType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#NonHumanActor' => [
            'name' => 'NonHumanActor',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Actor',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Organization' => [
            'name' => 'Organization',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAgent',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#basedAtLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactDetails' => '3.3',
                'https://onerecord.iata.org/ns/cargo#contactPersons' => '3.2',
                'https://onerecord.iata.org/ns/cargo#name' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#parentOrganization' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shortName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#subOrganization' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#OtherCharge' => [
            'name' => 'OtherCharge',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#chargePaymentType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#chargeQuantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#entitlement' => '3.2',
                'https://onerecord.iata.org/ns/cargo#locationIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherChargeAmount' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherChargeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#reasonDescription' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#OtherIdentifier' => [
            'name' => 'OtherIdentifier',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#otherIdentifierType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#textualValue' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PackagingType' => [
            'name' => 'PackagingType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#appliedOnPieces' => '3.2',
                'https://onerecord.iata.org/ns/cargo#description' => '3.2',
                'https://onerecord.iata.org/ns/cargo#typeCode' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Party' => [
            'name' => 'Party',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#accountNumbers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#partyDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#partyRole' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Person' => [
            'name' => 'Person',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Actor',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#contactDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contactRole' => '3.2',
                'https://onerecord.iata.org/ns/cargo#department' => '3.2',
                'https://onerecord.iata.org/ns/cargo#documents' => '3.2',
                'https://onerecord.iata.org/ns/cargo#employeeId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#firstName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#jobTitle' => '3.2',
                'https://onerecord.iata.org/ns/cargo#lastName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#middleName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#salutation' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject' => [
            'name' => 'PhysicalLogisticsObject',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#attachedIotDevices' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedInActions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.3',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Piece' => [
            'name' => 'Piece',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#coload' => '3.2',
                'https://onerecord.iata.org/ns/cargo#containedItems' => '3.2',
                'https://onerecord.iata.org/ns/cargo#containedPieces' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contentProductionCountry' => '3.2',
                'https://onerecord.iata.org/ns/cargo#contentProducts' => '3.2',
                'https://onerecord.iata.org/ns/cargo#customsInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#dimensions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#fulfillsUldTypeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#goodsDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#grossWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#inPiece' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#nvdForCarriage' => '3.2',
                'https://onerecord.iata.org/ns/cargo#nvdForCustoms' => '3.2',
                'https://onerecord.iata.org/ns/cargo#ofShipment' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#packageMarkCoded' => '3.2',
                'https://onerecord.iata.org/ns/cargo#packagedeIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#packagingType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#securityDeclarations' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shippingMarks' => '3.2',
                'https://onerecord.iata.org/ns/cargo#slac' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specialHandlingCodes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#stackable' => '3.2',
                'https://onerecord.iata.org/ns/cargo#temperatureInstructions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#textualHandlingInstructions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#turnable' => '3.2',
                'https://onerecord.iata.org/ns/cargo#upid' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PieceDg' => [
            'name' => 'PieceDg',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#allPackedInOneIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#dgDeclaration' => '3.2',
                'https://onerecord.iata.org/ns/cargo#overpackCriticalitySafetyIndexNumeric' => '3.2',
                'https://onerecord.iata.org/ns/cargo#overpackIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#overpackT1' => '3.2',
                'https://onerecord.iata.org/ns/cargo#overpackTypeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#qValueNumeric' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PieceGroup' => [
            'name' => 'PieceGroup',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#dryIceWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceGroupCount' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceGroupGrossWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieceGroupId' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals' => [
            'name' => 'PieceLiveAnimals',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#acquisitionDateTime' => '3.2',
                'https://onerecord.iata.org/ns/cargo#annualQuotaQuantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#associatedEpermit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#categoryCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#exportTradeCountry' => '3.2',
                'https://onerecord.iata.org/ns/cargo#goodsTypeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#goodsTypeExtensionCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#originReferencePermitDateTime' => '3.2',
                'https://onerecord.iata.org/ns/cargo#originReferencePermitId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#originReferencePermitTypeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#originTradeCountry' => '3.2',
                'https://onerecord.iata.org/ns/cargo#quantityAnimals' => '3.2',
                'https://onerecord.iata.org/ns/cargo#speciesCommonName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#speciesScientificName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specimenDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specimenTypeCode' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Price' => [
            'name' => 'Price',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#chargeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#forBookingOption' => '3.2',
                'https://onerecord.iata.org/ns/cargo#grandTotal' => '3.2',
                'https://onerecord.iata.org/ns/cargo#ratings' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Product' => [
            'name' => 'Product',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#commodityItemNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#describedObjects' => '3.2',
                'https://onerecord.iata.org/ns/cargo#description' => '3.2',
                'https://onerecord.iata.org/ns/cargo#hsCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#hsCommodityDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#hsCommodityName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#hsType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#manufacturer' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherCharacteristics' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uniqueIdentifier' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ProductDg' => [
            'name' => 'ProductDg',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#additionalHazardClassificationId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#authorizationInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#dgRadioactiveMaterial' => '3.2',
                'https://onerecord.iata.org/ns/cargo#explosiveCompatibilityGroupCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#hazardClassificationId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#packagingDangerLevelCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#packingInstructionNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#properShippingName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#specialProvisionId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#technicalName' => '3.2',
                'https://onerecord.iata.org/ns/cargo#unNumber' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PublicAuthority' => [
            'name' => 'PublicAuthority',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Question' => [
            'name' => 'Question',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#answer' => '3.2',
                'https://onerecord.iata.org/ns/cargo#answerOptionsText' => '3.2',
                'https://onerecord.iata.org/ns/cargo#answerOptionsValue' => '3.2',
                'https://onerecord.iata.org/ns/cargo#checkTemplate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#longText' => '3.2',
                'https://onerecord.iata.org/ns/cargo#questionNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#questionSection' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shortText' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Ranges' => [
            'name' => 'Ranges',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#maximumQuantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#minimumQuantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rateClassCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldRateClassType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#unitBasis' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Ratings' => [
            'name' => 'Ratings',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#billingChargeIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#chargeDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#chargePaymentType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#chargeType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#entitlement' => '3.2',
                'https://onerecord.iata.org/ns/cargo#forPrices' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherChargeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#priceReferenceId' => '3.2',
                'https://onerecord.iata.org/ns/cargo#priceSpecification' => '3.2',
                'https://onerecord.iata.org/ns/cargo#quantity' => '3.2',
                'https://onerecord.iata.org/ns/cargo#ranges' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rcp' => '3.2',
                'https://onerecord.iata.org/ns/cargo#subTotal' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#RegulatedEntity' => [
            'name' => 'RegulatedEntity',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#owningOrganization' => '3.2',
                'https://onerecord.iata.org/ns/cargo#regulatedEntityCategory' => '3.2',
                'https://onerecord.iata.org/ns/cargo#regulatedEntityExpiryDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#regulatedEntityIdentifier' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#SecurityDeclaration' => [
            'name' => 'SecurityDeclaration',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#additionalSecurityInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#groundsForExemption' => '3.2',
                'https://onerecord.iata.org/ns/cargo#issuedBy' => '3.2',
                'https://onerecord.iata.org/ns/cargo#issuedForPiece' => '3.2',
                'https://onerecord.iata.org/ns/cargo#issuedForShipment' => '3.3',
                'https://onerecord.iata.org/ns/cargo#issuedOn' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherRegulatedEntities' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherScreeningMethods' => '3.2',
                'https://onerecord.iata.org/ns/cargo#receivedFrom' => '3.2',
                'https://onerecord.iata.org/ns/cargo#regulatedEntitiesReceivedFrom' => '3.3',
                'https://onerecord.iata.org/ns/cargo#regulatedEntityAcceptor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#regulatedEntityIssuer' => '3.2',
                'https://onerecord.iata.org/ns/cargo#screeningMethods' => '3.2',
                'https://onerecord.iata.org/ns/cargo#securityStatus' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Sensor' => [
            'name' => 'Sensor',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#description' => '3.2',
                'https://onerecord.iata.org/ns/cargo#measurements' => '3.2',
                'https://onerecord.iata.org/ns/cargo#name' => '3.2',
                'https://onerecord.iata.org/ns/cargo#partOfIotDevice' => '3.2',
                'https://onerecord.iata.org/ns/cargo#sensorType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serialNumber' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#SensorType' => [
            'name' => 'SensorType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Shipment' => [
            'name' => 'Shipment',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#customsInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#goodsDescription' => '3.2',
                'https://onerecord.iata.org/ns/cargo#incoterms' => '3.2',
                'https://onerecord.iata.org/ns/cargo#insurance' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#pieces' => '3.2',
                'https://onerecord.iata.org/ns/cargo#securityDeclarations' => '3.3',
                'https://onerecord.iata.org/ns/cargo#textualHandlingInstructions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#totalDimensions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#totalGrossWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#totalVolume' => '3.3',
                'https://onerecord.iata.org/ns/cargo#waybill' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#StationRemarks' => [
            'name' => 'StationRemarks',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#remarksText' => '3.2',
                'https://onerecord.iata.org/ns/cargo#station' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#StatusUpdateEvent' => [
            'name' => 'StatusUpdateEvent',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#eventCode' => '3.3',
                'https://onerecord.iata.org/ns/cargo#eventFor' => '3.3',
                'https://onerecord.iata.org/ns/cargo#notifiedOrganization' => '3.3',
                'https://onerecord.iata.org/ns/cargo#recordedPieceCount' => '3.3',
                'https://onerecord.iata.org/ns/cargo#recordedVolume' => '3.3',
                'https://onerecord.iata.org/ns/cargo#recordedWeight' => '3.3',
                'https://onerecord.iata.org/ns/cargo#transferredFrom' => '3.3',
                'https://onerecord.iata.org/ns/cargo#transferredTo' => '3.3',
                'https://onerecord.iata.org/ns/cargo#transportMovementReference' => '3.3',
            ],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Storage' => [
            'name' => 'Storage',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#storingActions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#storingIdentifier' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Storing' => [
            'name' => 'Storing',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#storagePlaceIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#storedObjects' => '3.2',
                'https://onerecord.iata.org/ns/cargo#storingType' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#StoringType' => [
            'name' => 'StoringType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#TemperatureInstructions' => [
            'name' => 'TemperatureInstructions',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#maxTemperature' => '3.2',
                'https://onerecord.iata.org/ns/cargo#minTemperature' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#TransportLegs' => [
            'name' => 'TransportLegs',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#arrivalDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#arrivalLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#co2Emissions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#departureDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#departureLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#legNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#operatingTransportMeans' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportMeansServiceType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportMeansType' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#TransportMeans' => [
            'name' => 'TransportMeans',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#operatedTransportMovement' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportOrganization' => '3.2',
                'https://onerecord.iata.org/ns/cargo#typicalCo2Coefficient' => '3.2',
                'https://onerecord.iata.org/ns/cargo#typicalFuelConsumption' => '3.2',
                'https://onerecord.iata.org/ns/cargo#vehicleModel' => '3.2',
                'https://onerecord.iata.org/ns/cargo#vehicleRegistration' => '3.2',
                'https://onerecord.iata.org/ns/cargo#vehicleSize' => '3.2',
                'https://onerecord.iata.org/ns/cargo#vehicleType' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#TransportMovement' => [
            'name' => 'TransportMovement',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#arrivalLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#co2Emissions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#departureLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#distanceCalculated' => '3.2',
                'https://onerecord.iata.org/ns/cargo#distanceMeasured' => '3.2',
                'https://onerecord.iata.org/ns/cargo#fuelAmountCalculated' => '3.2',
                'https://onerecord.iata.org/ns/cargo#fuelAmountMeasured' => '3.2',
                'https://onerecord.iata.org/ns/cargo#fuelType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadingActions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#modeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#modeQualifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#movementTimes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#operatingParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#operatingTransportMeans' => '3.2',
                'https://onerecord.iata.org/ns/cargo#seal' => '3.2',
                'https://onerecord.iata.org/ns/cargo#transportIdentifier' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ULD' => [
            'name' => 'ULD',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#ataDesignator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#damageFlag' => '3.2',
                'https://onerecord.iata.org/ns/cargo#demurrageCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadingIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#numberOfDoors' => '3.2',
                'https://onerecord.iata.org/ns/cargo#numberOfFittings' => '3.2',
                'https://onerecord.iata.org/ns/cargo#numberOfNets' => '3.2',
                'https://onerecord.iata.org/ns/cargo#numberOfStraps' => '3.2',
                'https://onerecord.iata.org/ns/cargo#odlnCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#ownerCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#sealNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serviceabilityCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldSerialNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldTypeCode' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ULDBasicPiece' => [
            'name' => 'ULDBasicPiece',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#slac' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldLoadingIndicator' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ULDSpecificPiece' => [
            'name' => 'ULDSpecificPiece',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#slac' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldContourCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldSerialNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldType' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#UnitComposition' => [
            'name' => 'UnitComposition',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#compositionActions' => '3.2',
                'https://onerecord.iata.org/ns/cargo#compositionIdentifier' => '3.2',
                'https://onerecord.iata.org/ns/cargo#loadingUnit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#slac' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#UnitsPreference' => [
            'name' => 'UnitsPreference',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#currency' => '3.2',
                'https://onerecord.iata.org/ns/cargo#dimensionsUnit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#temperatureUnit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#volumeUnit' => '3.2',
                'https://onerecord.iata.org/ns/cargo#weightUnit' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Value' => [
            'name' => 'Value',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#numericalValue' => '3.2',
                'https://onerecord.iata.org/ns/cargo#unit' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#VolumePieceGroup' => [
            'name' => 'VolumePieceGroup',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#stackable' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#VolumetricWeight' => [
            'name' => 'VolumetricWeight',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#chargeableWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#conversionFactor' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#Waybill' => [
            'name' => 'Waybill',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#accountingInformation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#accountingNotes' => '3.2',
                'https://onerecord.iata.org/ns/cargo#agentReference' => '3.3',
                'https://onerecord.iata.org/ns/cargo#arrivalLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#billingDetails' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrierChargeCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrierDeclarationDate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrierDeclarationPlace' => '3.2',
                'https://onerecord.iata.org/ns/cargo#carrierDeclarationSignature' => '3.2',
                'https://onerecord.iata.org/ns/cargo#consignorDeclarationSignature' => '3.2',
                'https://onerecord.iata.org/ns/cargo#customsOriginCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#declaredValueForCarriage' => '3.2',
                'https://onerecord.iata.org/ns/cargo#declaredValueForCustoms' => '3.2',
                'https://onerecord.iata.org/ns/cargo#departureLocation' => '3.2',
                'https://onerecord.iata.org/ns/cargo#destinationCharges' => '3.2',
                'https://onerecord.iata.org/ns/cargo#destinationCurrencyRate' => '3.2',
                'https://onerecord.iata.org/ns/cargo#houseWaybills' => '3.2',
                'https://onerecord.iata.org/ns/cargo#involvedParties' => '3.2',
                'https://onerecord.iata.org/ns/cargo#masterWaybill' => '3.2',
                'https://onerecord.iata.org/ns/cargo#modularCheckNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherCharges' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherChargesIndicator' => '3.2',
                'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => '3.3',
                'https://onerecord.iata.org/ns/cargo#referredBookingOption' => '3.2',
                'https://onerecord.iata.org/ns/cargo#serviceCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shipment' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shippingInfo' => '3.2',
                'https://onerecord.iata.org/ns/cargo#shippingRefNo' => '3.2',
                'https://onerecord.iata.org/ns/cargo#taxAmount' => '3.2',
                'https://onerecord.iata.org/ns/cargo#valuationCharge' => '3.3',
                'https://onerecord.iata.org/ns/cargo#waybillLineItems' => '3.2',
                'https://onerecord.iata.org/ns/cargo#waybillNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#waybillPrefix' => '3.2',
                'https://onerecord.iata.org/ns/cargo#waybillType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#weightValuationIndicator' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#WaybillLineItem' => [
            'name' => 'WaybillLineItem',
            'parents' => [],
            'properties' => [
                'https://onerecord.iata.org/ns/cargo#chargeableWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#conversionFactor' => '3.2',
                'https://onerecord.iata.org/ns/cargo#lineItemNumber' => '3.2',
                'https://onerecord.iata.org/ns/cargo#lineItemPackages' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rateCharge' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rateClassCode' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rateClassCodeBasic' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rateGrossWeight' => '3.2',
                'https://onerecord.iata.org/ns/cargo#ratePercentage' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rateSlac' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rateVolume' => '3.2',
                'https://onerecord.iata.org/ns/cargo#rcp' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldRateClassType' => '3.2',
                'https://onerecord.iata.org/ns/cargo#uldReferences' => '3.2',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#WaybillType' => [
            'name' => 'WaybillType',
            'parents' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'properties' => [],
            'since' => '3.2',
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
        'https://onerecord.iata.org/ns/cargo#accountNumberType' => [
            'name' => 'accountNumberType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#AccountType',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#accountNumbers' => [
            'name' => 'accountNumbers',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#AccountNumber',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#accountingInformation' => [
            'name' => 'accountingInformation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#accountingNoteIdentifier' => [
            'name' => 'accountingNoteIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#AccountingNote',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#accountingNoteText' => [
            'name' => 'accountingNoteText',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#AccountingNote',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#accountingNotes' => [
            'name' => 'accountingNotes',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#AccountingNote',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#acquisitionDateTime' => [
            'name' => 'acquisitionDateTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#actionEndTime' => [
            'name' => 'actionEndTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#actionStartTime' => [
            'name' => 'actionStartTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#actionTimeType' => [
            'name' => 'actionTimeType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ActionTimeType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#activity' => [
            'name' => 'activity',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ActivitySequence',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#activityLevelMeasure' => [
            'name' => 'activityLevelMeasure',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#activitySequences' => [
            'name' => 'activitySequences',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ActivitySequence',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsService',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#additionalHazardClassificationId' => [
            'name' => 'additionalHazardClassificationId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#additionalInformation' => [
            'name' => 'additionalInformation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#additionalSecurityInformation' => [
            'name' => 'additionalSecurityInformation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#address' => [
            'name' => 'address',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#addressCode' => [
            'name' => 'addressCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#adjustments' => [
            'name' => 'adjustments',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Adjustments',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#agentReference' => [
            'name' => 'agentReference',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#aircraftLimitationInformation' => [
            'name' => 'aircraftLimitationInformation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#aircraftPossibilityCode' => [
            'name' => 'aircraftPossibilityCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingPreferences',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#airlineCode' => [
            'name' => 'airlineCode',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Carrier',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#allPackedInOneIndicator' => [
            'name' => 'allPackedInOneIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#allotmentCode' => [
            'name' => 'allotmentCode',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#alternatives' => [
            'name' => 'alternatives',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#annualQuotaQuantity' => [
            'name' => 'annualQuotaQuantity',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#answer' => [
            'name' => 'answer',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Answer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#answerActor' => [
            'name' => 'answerActor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Actor',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Answer',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#answerOptionsText' => [
            'name' => 'answerOptionsText',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#answerOptionsValue' => [
            'name' => 'answerOptionsValue',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#answerValue' => [
            'name' => 'answerValue',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Answer',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#appliedOnPieces' => [
            'name' => 'appliedOnPieces',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PackagingType',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#arrivalDate' => [
            'name' => 'arrivalDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportLegs',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#arrivalLocation' => [
            'name' => 'arrivalLocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#associatedEpermit' => [
            'name' => 'associatedEpermit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#EpermitConsignment',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#associatedOrganization' => [
            'name' => 'associatedOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Actor',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ataDesignator' => [
            'name' => 'ataDesignator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#attachedIotDevices' => [
            'name' => 'attachedIotDevices',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#IotDevice',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#attachedToObject' => [
            'name' => 'attachedToObject',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#IotDevice',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#authorizationInformation' => [
            'name' => 'authorizationInformation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#awbAcceptanceDate' => [
            'name' => 'awbAcceptanceDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#awbDeliveryDate' => [
            'name' => 'awbDeliveryDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#awbExecutionDate' => [
            'name' => 'awbExecutionDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#awbUseIndicator' => [
            'name' => 'awbUseIndicator',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/AWBUseIndicator',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#basedAtLocation' => [
            'name' => 'basedAtLocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#batchNumber' => [
            'name' => 'batchNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#billingChargeIdentifier' => [
            'name' => 'billingChargeIdentifier',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#billingDetails' => [
            'name' => 'billingDetails',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#booking' => [
            'name' => 'booking',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Booking',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingRequest',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingOptions' => [
            'name' => 'bookingOptions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingPreference' => [
            'name' => 'bookingPreference',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingPreferences',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingRequest' => [
            'name' => 'bookingRequest',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingRequest',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Booking',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingSegments' => [
            'name' => 'bookingSegments',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingSegment',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingShipmentDetails' => [
            'name' => 'bookingShipmentDetails',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingStatus' => [
            'name' => 'bookingStatus',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingStatus',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Booking',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingTimes' => [
            'name' => 'bookingTimes',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingTimes',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#bookingToUpdate' => [
            'name' => 'bookingToUpdate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Booking',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#calculatedEmissions' => [
            'name' => 'calculatedEmissions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CO2Emissions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#calculationFor' => [
            'name' => 'calculationFor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CO2Emissions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#carrier' => [
            'name' => 'carrier',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Carrier',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#carrierChargeCode' => [
            'name' => 'carrierChargeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ChargeCode',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#carrierDeclarationDate' => [
            'name' => 'carrierDeclarationDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#carrierDeclarationPlace' => [
            'name' => 'carrierDeclarationPlace',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#carrierDeclarationSignature' => [
            'name' => 'carrierDeclarationSignature',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#carrierProduct' => [
            'name' => 'carrierProduct',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CarrierProduct',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#categoryCode' => [
            'name' => 'categoryCode',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#certifiedByActor' => [
            'name' => 'certifiedByActor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTotalResult',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#characteristicType' => [
            'name' => 'characteristicType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Characteristic',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#chargeCode' => [
            'name' => 'chargeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ChargeCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Price',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#chargeDescription' => [
            'name' => 'chargeDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#chargePaymentType' => [
            'name' => 'chargePaymentType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/PrepaidCollectIndicator',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#chargeQuantity' => [
            'name' => 'chargeQuantity',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#OtherCharge',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#chargeType' => [
            'name' => 'chargeType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#chargeableWeight' => [
            'name' => 'chargeableWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#VolumetricWeight',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#chargeableWeightForRate' => [
            'name' => 'chargeableWeightForRate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checkActions' => [
            'name' => 'checkActions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checkRemark' => [
            'name' => 'checkRemark',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTotalResult',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checkTemplate' => [
            'name' => 'checkTemplate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checkTotalResult' => [
            'name' => 'checkTotalResult',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CheckTotalResult',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checkedObject' => [
            'name' => 'checkedObject',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checker' => [
            'name' => 'checker',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Actor',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checks' => [
            'name' => 'checks',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#checksum' => [
            'name' => 'checksum',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#cityCode' => [
            'name' => 'cityCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#cityName' => [
            'name' => 'cityName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#co2Emissions' => [
            'name' => 'co2Emissions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CO2Emissions',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#code' => [
            'name' => 'code',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#codeDescription' => [
            'name' => 'codeDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#codeLevel' => [
            'name' => 'codeLevel',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#codeListName' => [
            'name' => 'codeListName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#codeListReference' => [
            'name' => 'codeListReference',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#codeListVersion' => [
            'name' => 'codeListVersion',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#coload' => [
            'name' => 'coload',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#commission' => [
            'name' => 'commission',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#commissionIndicator' => [
            'name' => 'commissionIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#commissionPercentage' => [
            'name' => 'commissionPercentage',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#commodityItemNumber' => [
            'name' => 'commodityItemNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#commodityItemNumberForRate' => [
            'name' => 'commodityItemNumberForRate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#complianceDeclarationText' => [
            'name' => 'complianceDeclarationText',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#composedMaterials' => [
            'name' => 'composedMaterials',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LoadingMaterial',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Composing',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#composedPieces' => [
            'name' => 'composedPieces',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Composing',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#compositionActions' => [
            'name' => 'compositionActions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Composing',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#UnitComposition',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#compositionIdentifier' => [
            'name' => 'compositionIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#UnitComposition',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#compositionType' => [
            'name' => 'compositionType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CompositionType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Composing',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#connectedSensors' => [
            'name' => 'connectedSensors',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Sensor',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#IotDevice',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#consignee' => [
            'name' => 'consignee',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#consignmentItems' => [
            'name' => 'consignmentItems',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitConsignment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#consignments' => [
            'name' => 'consignments',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#EpermitConsignment',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#consignorDeclarationSignature' => [
            'name' => 'consignorDeclarationSignature',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#consolidationIndicator' => [
            'name' => 'consolidationIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contactDetailType' => [
            'name' => 'contactDetailType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetail',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contactDetails' => [
            'name' => 'contactDetails',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetail',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contactPersons' => [
            'name' => 'contactPersons',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Actor',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contactRole' => [
            'name' => 'contactRole',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ContactRole',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#containedItems' => [
            'name' => 'containedItems',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#containedPieces' => [
            'name' => 'containedPieces',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contentCode' => [
            'name' => 'contentCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CustomsInformation',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contentOfDgProductRadioactive' => [
            'name' => 'contentOfDgProductRadioactive',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contentProductionCountry' => [
            'name' => 'contentProductionCountry',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#contentProducts' => [
            'name' => 'contentProducts',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#conversionFactor' => [
            'name' => 'conversionFactor',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#VolumetricWeight',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#copyIndicator' => [
            'name' => 'copyIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#correctionNumber' => [
            'name' => 'correctionNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Adjustments',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#correctionSerialNumber' => [
            'name' => 'correctionSerialNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Adjustments',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#country' => [
            'name' => 'country',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#coveringOrganization' => [
            'name' => 'coveringOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Insurance',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#createdAtLocation' => [
            'name' => 'createdAtLocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#creationDate' => [
            'name' => 'creationDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#criticalitySafetyIndexNumeric' => [
            'name' => 'criticalitySafetyIndexNumeric',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#currency' => [
            'name' => 'currency',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/CurrencyCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#UnitsPreference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#currencyUnit' => [
            'name' => 'currencyUnit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/CurrencyCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#customsInformation' => [
            'name' => 'customsInformation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CustomsInformation',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#customsOriginCode' => [
            'name' => 'customsOriginCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#damageFlag' => [
            'name' => 'damageFlag',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#date' => [
            'name' => 'date',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#declarationDate' => [
            'name' => 'declarationDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#declarationPlace' => [
            'name' => 'declarationPlace',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#declaredValueForCarriage' => [
            'name' => 'declaredValueForCarriage',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#declaredValueForCustoms' => [
            'name' => 'declaredValueForCustoms',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#demurrageCode' => [
            'name' => 'demurrageCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/DemurrageCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#densityGroupCode' => [
            'name' => 'densityGroupCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/DensityGroupCode',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#department' => [
            'name' => 'department',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#departureDate' => [
            'name' => 'departureDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportLegs',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#departureLocation' => [
            'name' => 'departureLocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#describedObjects' => [
            'name' => 'describedObjects',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#description' => [
            'name' => 'description',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#destinationCharges' => [
            'name' => 'destinationCharges',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#destinationCurrencyRate' => [
            'name' => 'destinationCurrencyRate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#detailedWaybill' => [
            'name' => 'detailedWaybill',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#deviceModel' => [
            'name' => 'deviceModel',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#IotDevice',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#dgDeclaration' => [
            'name' => 'dgDeclaration',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#DgDeclaration',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#dgRaTypeCode' => [
            'name' => 'dgRaTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/RaTypeCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#dgRadioactiveMaterial' => [
            'name' => 'dgRadioactiveMaterial',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#dimensions' => [
            'name' => 'dimensions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Dimensions',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#dimensionsForRate' => [
            'name' => 'dimensionsForRate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Dimensions',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#dimensionsUnit' => [
            'name' => 'dimensionsUnit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/DimensionsUnitCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#UnitsPreference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#direction' => [
            'name' => 'direction',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#DirectionType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#MovementTime',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#discount' => [
            'name' => 'discount',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#distanceCalculated' => [
            'name' => 'distanceCalculated',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#distanceMeasured' => [
            'name' => 'distanceMeasured',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#documentIdentifier' => [
            'name' => 'documentIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#documentLink' => [
            'name' => 'documentLink',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#documentName' => [
            'name' => 'documentName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#documentType' => [
            'name' => 'documentType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#documentVersion' => [
            'name' => 'documentVersion',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#documents' => [
            'name' => 'documents',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#dryIceWeight' => [
            'name' => 'dryIceWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#earliestAcceptanceTime' => [
            'name' => 'earliestAcceptanceTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingTimes',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#elevation' => [
            'name' => 'elevation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Geolocation',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#emergencyContact' => [
            'name' => 'emergencyContact',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ItemDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#employeeId' => [
            'name' => 'employeeId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#entitlement' => [
            'name' => 'entitlement',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/EntitlementCode',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#epermit' => [
            'name' => 'epermit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitConsignment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#epermitNumber' => [
            'name' => 'epermitNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#eventCode' => [
            'name' => 'eventCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#eventDate' => [
            'name' => 'eventDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#eventFor' => [
            'name' => 'eventFor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#eventLocation' => [
            'name' => 'eventLocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#eventName' => [
            'name' => 'eventName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#eventTimeType' => [
            'name' => 'eventTimeType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#EventTimeType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#events' => [
            'name' => 'events',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#examiningQuantity' => [
            'name' => 'examiningQuantity',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitConsignment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#exchangeRate' => [
            'name' => 'exchangeRate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#excludedViaPoints' => [
            'name' => 'excludedViaPoints',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingPreferences',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#exclusiveUseIndicator' => [
            'name' => 'exclusiveUseIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#executionStatus' => [
            'name' => 'executionStatus',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ExecutionStatus',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#expectedCommodity' => [
            'name' => 'expectedCommodity',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/CommodityCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#expectedHScode' => [
            'name' => 'expectedHScode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#expiryDate' => [
            'name' => 'expiryDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#explosiveCompatibilityGroupCode' => [
            'name' => 'explosiveCompatibilityGroupCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#exportTradeCountry' => [
            'name' => 'exportTradeCountry',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#externalReferences' => [
            'name' => 'externalReferences',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#firstName' => [
            'name' => 'firstName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#fissileExceptionIndicator' => [
            'name' => 'fissileExceptionIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#fissileExceptionReference' => [
            'name' => 'fissileExceptionReference',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#forBookingOption' => [
            'name' => 'forBookingOption',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#forBookingOptionRequest' => [
            'name' => 'forBookingOptionRequest',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#forBookingRequest' => [
            'name' => 'forBookingRequest',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingRequest',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#forEpermit' => [
            'name' => 'forEpermit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#forPrices' => [
            'name' => 'forPrices',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Price',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#forProductDg' => [
            'name' => 'forProductDg',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#fuelAmountCalculated' => [
            'name' => 'fuelAmountCalculated',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#fuelAmountMeasured' => [
            'name' => 'fuelAmountMeasured',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#fuelType' => [
            'name' => 'fuelType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#fulfillsUldTypeCode' => [
            'name' => 'fulfillsUldTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#geolocation' => [
            'name' => 'geolocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Geolocation',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#givenAtLocation' => [
            'name' => 'givenAtLocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Answer',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#goodsDescription' => [
            'name' => 'goodsDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#goodsDescriptionForRate' => [
            'name' => 'goodsDescriptionForRate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#goodsTypeCode' => [
            'name' => 'goodsTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/GoodsTypeCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#goodsTypeExtensionCode' => [
            'name' => 'goodsTypeExtensionCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#grandTotal' => [
            'name' => 'grandTotal',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Price',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#grossWeight' => [
            'name' => 'grossWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#grossWeightForRate' => [
            'name' => 'grossWeightForRate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#groundsForExemption' => [
            'name' => 'groundsForExemption',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ScreeningExemption',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#handlingInformation' => [
            'name' => 'handlingInformation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#handlingServiceFor' => [
            'name' => 'handlingServiceFor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#hazardClassificationId' => [
            'name' => 'hazardClassificationId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#height' => [
            'name' => 'height',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Dimensions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#houseWaybills' => [
            'name' => 'houseWaybills',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#hsCode' => [
            'name' => 'hsCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#hsCodeForRate' => [
            'name' => 'hsCodeForRate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#hsCommodityDescription' => [
            'name' => 'hsCommodityDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#hsCommodityName' => [
            'name' => 'hsCommodityName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#hsType' => [
            'name' => 'hsType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#iataCargoAgentCode' => [
            'name' => 'iataCargoAgentCode',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Company',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#iataCargoAgentLocationIdentifier' => [
            'name' => 'iataCargoAgentLocationIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Company',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#inPiece' => [
            'name' => 'inPiece',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#inUnitComposition' => [
            'name' => 'inUnitComposition',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#UnitComposition',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#includedViaPoints' => [
            'name' => 'includedViaPoints',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingPreferences',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#incoterms' => [
            'name' => 'incoterms',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#insurance' => [
            'name' => 'insurance',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Insurance',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#insuredAmount' => [
            'name' => 'insuredAmount',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Insurance',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#insuredShipments' => [
            'name' => 'insuredShipments',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Insurance',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#involvedInActions' => [
            'name' => 'involvedInActions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#involvedParties' => [
            'name' => 'involvedParties',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Party',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#isotopeId' => [
            'name' => 'isotopeId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#isotopeName' => [
            'name' => 'isotopeName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#isotopes' => [
            'name' => 'isotopes',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#issuedBy' => [
            'name' => 'issuedBy',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#issuedForPiece' => [
            'name' => 'issuedForPiece',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#issuedForShipment' => [
            'name' => 'issuedForShipment',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#issuedForWaybill' => [
            'name' => 'issuedForWaybill',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Booking',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#issuedOn' => [
            'name' => 'issuedOn',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#itemQuantity' => [
            'name' => 'itemQuantity',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#jobTitle' => [
            'name' => 'jobTitle',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#knownShipper' => [
            'name' => 'knownShipper',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#lastName' => [
            'name' => 'lastName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#latestAcceptanceTime' => [
            'name' => 'latestAcceptanceTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingTimes',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#latestArrivalTime' => [
            'name' => 'latestArrivalTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingTimes',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#latitude' => [
            'name' => 'latitude',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Geolocation',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#legNumber' => [
            'name' => 'legNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportLegs',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#legacyTemplate' => [
            'name' => 'legacyTemplate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#length' => [
            'name' => 'length',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Dimensions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#lineItemNumber' => [
            'name' => 'lineItemNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#lineItemPackages' => [
            'name' => 'lineItemPackages',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LineItemPackage',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadType' => [
            'name' => 'loadType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LoadType',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadedMaterials' => [
            'name' => 'loadedMaterials',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LoadingMaterial',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Loading',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadedPieces' => [
            'name' => 'loadedPieces',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Loading',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadedUnits' => [
            'name' => 'loadedUnits',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Loading',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadingActions' => [
            'name' => 'loadingActions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Loading',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadingIndicator' => [
            'name' => 'loadingIndicator',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadingPositionIdentifier' => [
            'name' => 'loadingPositionIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Loading',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadingType' => [
            'name' => 'loadingType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LoadingType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Loading',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#loadingUnit' => [
            'name' => 'loadingUnit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#locationCodes' => [
            'name' => 'locationCodes',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#locationIndicator' => [
            'name' => 'locationIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#locationName' => [
            'name' => 'locationName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#locationType' => [
            'name' => 'locationType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#longText' => [
            'name' => 'longText',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#longitude' => [
            'name' => 'longitude',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Geolocation',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#lotNumber' => [
            'name' => 'lotNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#lowDispersibleIndicator' => [
            'name' => 'lowDispersibleIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#manufacturer' => [
            'name' => 'manufacturer',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#masterWaybill' => [
            'name' => 'masterWaybill',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#materialModel' => [
            'name' => 'materialModel',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingMaterial',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#materialType' => [
            'name' => 'materialType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingMaterial',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#maxSegments' => [
            'name' => 'maxSegments',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingPreferences',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#maxTemperature' => [
            'name' => 'maxTemperature',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TemperatureInstructions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#maximumQuantity' => [
            'name' => 'maximumQuantity',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ranges',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#measurementTimestamp' => [
            'name' => 'measurementTimestamp',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Measurement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#measurementValue' => [
            'name' => 'measurementValue',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Measurement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#measurements' => [
            'name' => 'measurements',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Measurement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Sensor',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#methodName' => [
            'name' => 'methodName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CO2Emissions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#methodVersion' => [
            'name' => 'methodVersion',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CO2Emissions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#middleName' => [
            'name' => 'middleName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#minTemperature' => [
            'name' => 'minTemperature',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TemperatureInstructions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#minimumQuantity' => [
            'name' => 'minimumQuantity',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ranges',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#modeCode' => [
            'name' => 'modeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ModeCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#modeQualifier' => [
            'name' => 'modeQualifier',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ModeQualifier',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#modularCheckNumber' => [
            'name' => 'modularCheckNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#movementMilestone' => [
            'name' => 'movementMilestone',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/MovementIndicator',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#MovementTime',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#movementTimeType' => [
            'name' => 'movementTimeType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#MovementTimeType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#MovementTime',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#movementTimes' => [
            'name' => 'movementTimes',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#MovementTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#movementTimestamp' => [
            'name' => 'movementTimestamp',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#MovementTime',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#name' => [
            'name' => 'name',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#nbCorrections' => [
            'name' => 'nbCorrections',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#netWeightMeasure' => [
            'name' => 'netWeightMeasure',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ItemDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#note' => [
            'name' => 'note',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CustomsInformation',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#notifiedOrganization' => [
            'name' => 'notifiedOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#numberOfDoors' => [
            'name' => 'numberOfDoors',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#numberOfFittings' => [
            'name' => 'numberOfFittings',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#numberOfNets' => [
            'name' => 'numberOfNets',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#numberOfStraps' => [
            'name' => 'numberOfStraps',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#numericalValue' => [
            'name' => 'numericalValue',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#nvdForCarriage' => [
            'name' => 'nvdForCarriage',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#nvdForCustoms' => [
            'name' => 'nvdForCustoms',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ociLineNumber' => [
            'name' => 'ociLineNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#odlnCode' => [
            'name' => 'odlnCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ofProduct' => [
            'name' => 'ofProduct',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ofShipment' => [
            'name' => 'ofShipment',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#offerValidFrom' => [
            'name' => 'offerValidFrom',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#offerValidTo' => [
            'name' => 'offerValidTo',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#onTransportMeans' => [
            'name' => 'onTransportMeans',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Loading',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#onsiteActions' => [
            'name' => 'onsiteActions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#operatedTransportMovement' => [
            'name' => 'operatedTransportMovement',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#operatingParties' => [
            'name' => 'operatingParties',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Party',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#operatingTransportMeans' => [
            'name' => 'operatingTransportMeans',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#originReferencePermitDateTime' => [
            'name' => 'originReferencePermitDateTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#originReferencePermitId' => [
            'name' => 'originReferencePermitId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#originReferencePermitTypeCode' => [
            'name' => 'originReferencePermitTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#originTradeCountry' => [
            'name' => 'originTradeCountry',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#originator' => [
            'name' => 'originator',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Company',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherCharacteristics' => [
            'name' => 'otherCharacteristics',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Characteristic',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherChargeAmount' => [
            'name' => 'otherChargeAmount',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#OtherCharge',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherChargeCode' => [
            'name' => 'otherChargeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/OtherChargeCode',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherCharges' => [
            'name' => 'otherCharges',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#OtherCharge',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherChargesIndicator' => [
            'name' => 'otherChargesIndicator',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/PrepaidCollectIndicator',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherCustomsInformation' => [
            'name' => 'otherCustomsInformation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CustomsInformation',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherIdentifierType' => [
            'name' => 'otherIdentifierType',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#OtherIdentifier',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherIdentifiers' => [
            'name' => 'otherIdentifiers',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#OtherIdentifier',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherRegulatedEntities' => [
            'name' => 'otherRegulatedEntities',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#otherScreeningMethods' => [
            'name' => 'otherScreeningMethods',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#overpackCriticalitySafetyIndexNumeric' => [
            'name' => 'overpackCriticalitySafetyIndexNumeric',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#overpackIndicator' => [
            'name' => 'overpackIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#overpackT1' => [
            'name' => 'overpackT1',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#overpackTypeCode' => [
            'name' => 'overpackTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ownerCode' => [
            'name' => 'ownerCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#owningOrganization' => [
            'name' => 'owningOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packageGrossWeight' => [
            'name' => 'packageGrossWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packageMarkCoded' => [
            'name' => 'packageMarkCoded',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/PackageMarkCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packageSlac' => [
            'name' => 'packageSlac',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packageVolume' => [
            'name' => 'packageVolume',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packagedeIdentifier' => [
            'name' => 'packagedeIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packagingDangerLevelCode' => [
            'name' => 'packagingDangerLevelCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/PackagingDangerLevelCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packagingType' => [
            'name' => 'packagingType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#PackagingType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#packingInstructionNumber' => [
            'name' => 'packingInstructionNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#parentOrganization' => [
            'name' => 'parentOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#partOfIotDevice' => [
            'name' => 'partOfIotDevice',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#IotDevice',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Sensor',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#partialEventIndicator' => [
            'name' => 'partialEventIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#partyDetails' => [
            'name' => 'partyDetails',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAgent',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Party',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#partyRole' => [
            'name' => 'partyRole',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Party',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#passed' => [
            'name' => 'passed',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTotalResult',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#performedAt' => [
            'name' => 'performedAt',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#permitTypeCode' => [
            'name' => 'permitTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#permitTypeOtherDescription' => [
            'name' => 'permitTypeOtherDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#physicalChemicalForm' => [
            'name' => 'physicalChemicalForm',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/RadioactiveMaterialClassification',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceCountForRate' => [
            'name' => 'pieceCountForRate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceGroupCount' => [
            'name' => 'pieceGroupCount',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceGroupGrossWeight' => [
            'name' => 'pieceGroupGrossWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceGroupId' => [
            'name' => 'pieceGroupId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceGroups' => [
            'name' => 'pieceGroups',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#PieceGroup',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceHeight' => [
            'name' => 'pieceHeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoosePiece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceLength' => [
            'name' => 'pieceLength',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoosePiece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceReferences' => [
            'name' => 'pieceReferences',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceWeight' => [
            'name' => 'pieceWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoosePiece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieceWidth' => [
            'name' => 'pieceWidth',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoosePiece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#pieces' => [
            'name' => 'pieces',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#postOfficeBox' => [
            'name' => 'postOfficeBox',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#postalCode' => [
            'name' => 'postalCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#preferredTransportId' => [
            'name' => 'preferredTransportId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingPreferences',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#prefix' => [
            'name' => 'prefix',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Carrier',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#price' => [
            'name' => 'price',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Price',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#priceReferenceId' => [
            'name' => 'priceReferenceId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#priceSpecification' => [
            'name' => 'priceSpecification',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#productCode' => [
            'name' => 'productCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CarrierProduct',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#productDescription' => [
            'name' => 'productDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CarrierProduct',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#productionCountry' => [
            'name' => 'productionCountry',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#productionCountryForRate' => [
            'name' => 'productionCountryForRate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#productionDate' => [
            'name' => 'productionDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#properShippingName' => [
            'name' => 'properShippingName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#qValueNumeric' => [
            'name' => 'qValueNumeric',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#quantity' => [
            'name' => 'quantity',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#quantityAnimals' => [
            'name' => 'quantityAnimals',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#quantityForUnitPrice' => [
            'name' => 'quantityForUnitPrice',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#question' => [
            'name' => 'question',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Answer',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#questionNumber' => [
            'name' => 'questionNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#questionSection' => [
            'name' => 'questionSection',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#questions' => [
            'name' => 'questions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ranges' => [
            'name' => 'ranges',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Ranges',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#rateCharge' => [
            'name' => 'rateCharge',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#rateClassCode' => [
            'name' => 'rateClassCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/RateClassCode',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#rateClassCodeBasic' => [
            'name' => 'rateClassCodeBasic',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/BasicRateClassCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#rateGrossWeight' => [
            'name' => 'rateGrossWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ratePercentage' => [
            'name' => 'ratePercentage',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#rateSlac' => [
            'name' => 'rateSlac',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#rateVolume' => [
            'name' => 'rateVolume',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ratings' => [
            'name' => 'ratings',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Price',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#rcp' => [
            'name' => 'rcp',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#reasonDescription' => [
            'name' => 'reasonDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#OtherCharge',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#reasonsForAdjustments' => [
            'name' => 'reasonsForAdjustments',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Adjustments',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#receivedFrom' => [
            'name' => 'receivedFrom',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.3',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#recordedGeolocation' => [
            'name' => 'recordedGeolocation',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Geolocation',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Measurement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#recordedPieceCount' => [
            'name' => 'recordedPieceCount',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#recordedVolume' => [
            'name' => 'recordedVolume',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#recordedWeight' => [
            'name' => 'recordedWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#recordingActor' => [
            'name' => 'recordingActor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Actor',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#recordingOrganization' => [
            'name' => 'recordingOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsEvent',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#referenceForObjects' => [
            'name' => 'referenceForObjects',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ExternalReference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#referredBookingOption' => [
            'name' => 'referredBookingOption',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Booking',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#regionCode' => [
            'name' => 'regionCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#regulatedEntitiesReceivedFrom' => [
            'name' => 'regulatedEntitiesReceivedFrom',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#regulatedEntityAcceptor' => [
            'name' => 'regulatedEntityAcceptor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#regulatedEntityCategory' => [
            'name' => 'regulatedEntityCategory',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/RegulatedEntityCategoryCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#regulatedEntityExpiryDate' => [
            'name' => 'regulatedEntityExpiryDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#regulatedEntityIdentifier' => [
            'name' => 'regulatedEntityIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#regulatedEntityIssuer' => [
            'name' => 'regulatedEntityIssuer',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#RegulatedEntity',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#remarks' => [
            'name' => 'remarks',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#remarksText' => [
            'name' => 'remarksText',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#StationRemarks',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#reportableQuantity' => [
            'name' => 'reportableQuantity',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ItemDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#requestMatch' => [
            'name' => 'requestMatch',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#resultOfCheck' => [
            'name' => 'resultOfCheck',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTotalResult',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#resultValue' => [
            'name' => 'resultValue',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTotalResult',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#salutation' => [
            'name' => 'salutation',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Person',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#screeningMethods' => [
            'name' => 'screeningMethods',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ScreeningMethod',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#seal' => [
            'name' => 'seal',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#sealNumber' => [
            'name' => 'sealNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#securityDeclarations' => [
            'name' => 'securityDeclarations',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#securityStampId' => [
            'name' => 'securityStampId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#securityStatus' => [
            'name' => 'securityStatus',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/SecurityStatus',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#SecurityDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#sensorType' => [
            'name' => 'sensorType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Sensor',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#sequenceNumber' => [
            'name' => 'sequenceNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ActivitySequence',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#serialNumber' => [
            'name' => 'serialNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#servedActivity' => [
            'name' => 'servedActivity',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsAction',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#servedServices' => [
            'name' => 'servedServices',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsService',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsActivity',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#serviceCode' => [
            'name' => 'serviceCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ServiceCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#serviceForWaybills' => [
            'name' => 'serviceForWaybills',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#serviceLevelCode' => [
            'name' => 'serviceLevelCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CarrierProduct',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#serviceProvider' => [
            'name' => 'serviceProvider',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Party',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#serviceRequestor' => [
            'name' => 'serviceRequestor',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Party',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#serviceabilityCode' => [
            'name' => 'serviceabilityCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ULDConditionCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#shipment' => [
            'name' => 'shipment',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#shipperDeclarationText' => [
            'name' => 'shipperDeclarationText',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgDeclaration',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#shippingInfo' => [
            'name' => 'shippingInfo',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#shippingMarks' => [
            'name' => 'shippingMarks',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#shippingRefNo' => [
            'name' => 'shippingRefNo',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#shortName' => [
            'name' => 'shortName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#shortText' => [
            'name' => 'shortText',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Question',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#signatoryCompany' => [
            'name' => 'signatoryCompany',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Company',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#signatoryRole' => [
            'name' => 'signatoryRole',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/SignatoryRole',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#signatureDate' => [
            'name' => 'signatureDate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#signatureStatement' => [
            'name' => 'signatureStatement',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#signatureTypeCode' => [
            'name' => 'signatureTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/SignatureTypeCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#signatures' => [
            'name' => 'signatures',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#EpermitSignature',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#skeletonIndicator' => [
            'name' => 'skeletonIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LogisticsObject',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#slac' => [
            'name' => 'slac',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#slacForRate' => [
            'name' => 'slacForRate',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#spaceAllocationCode' => [
            'name' => 'spaceAllocationCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#specialConditions' => [
            'name' => 'specialConditions',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#specialFormIndicator' => [
            'name' => 'specialFormIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgRadioactiveIsotope',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#specialHandlingCodes' => [
            'name' => 'specialHandlingCodes',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#specialProvisionId' => [
            'name' => 'specialProvisionId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#specialServiceRequests' => [
            'name' => 'specialServiceRequests',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#speciesCommonName' => [
            'name' => 'speciesCommonName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#speciesScientificName' => [
            'name' => 'speciesScientificName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#specimenDescription' => [
            'name' => 'specimenDescription',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#specimenTypeCode' => [
            'name' => 'specimenTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PieceLiveAnimals',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#stackable' => [
            'name' => 'stackable',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#station' => [
            'name' => 'station',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#StationRemarks',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#stationRemarks' => [
            'name' => 'stationRemarks',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#StationRemarks',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#statusBookingOption' => [
            'name' => 'statusBookingOption',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOption',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#storagePlaceIdentifier' => [
            'name' => 'storagePlaceIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Storing',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#storedObjects' => [
            'name' => 'storedObjects',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#PhysicalLogisticsObject',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Storing',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#storingActions' => [
            'name' => 'storingActions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Storing',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Storage',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#storingIdentifier' => [
            'name' => 'storingIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Storage',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#storingType' => [
            'name' => 'storingType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#StoringType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Storing',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#streetAddressLines' => [
            'name' => 'streetAddressLines',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#subLocationOf' => [
            'name' => 'subLocationOf',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#subLocations' => [
            'name' => 'subLocations',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Location',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#subOrganization' => [
            'name' => 'subOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#subTotal' => [
            'name' => 'subTotal',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#double',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ratings',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#subjectCode' => [
            'name' => 'subjectCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CustomsInformation',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#supplementaryInfoPrefix' => [
            'name' => 'supplementaryInfoPrefix',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ItemDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#supplementaryInfoSuffix' => [
            'name' => 'supplementaryInfoSuffix',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ItemDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#tareWeight' => [
            'name' => 'tareWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#targetCountry' => [
            'name' => 'targetCountry',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#taxAmount' => [
            'name' => 'taxAmount',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#taxDueAgent' => [
            'name' => 'taxDueAgent',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#taxDueAirline' => [
            'name' => 'taxDueAirline',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#technicalName' => [
            'name' => 'technicalName',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#temperatureInstructions' => [
            'name' => 'temperatureInstructions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#TemperatureInstructions',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingShipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#temperatureUnit' => [
            'name' => 'temperatureUnit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/TemperatureUnitCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#UnitsPreference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#templatePurpose' => [
            'name' => 'templatePurpose',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#text' => [
            'name' => 'text',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Answer',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#textualHandlingInstructions' => [
            'name' => 'textualHandlingInstructions',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#textualPostCode' => [
            'name' => 'textualPostCode',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Address',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#textualValue' => [
            'name' => 'textualValue',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#timeOfAvailability' => [
            'name' => 'timeOfAvailability',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingTimes',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#timePreferences' => [
            'name' => 'timePreferences',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingTimes',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#totalDimensions' => [
            'name' => 'totalDimensions',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Dimensions',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.3',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#totalGrossWeight' => [
            'name' => 'totalGrossWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#totalTransitTime' => [
            'name' => 'totalTransitTime',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#duration',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BookingTimes',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#totalVolume' => [
            'name' => 'totalVolume',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#VolumePieceGroup',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#totalVolumetricWeight' => [
            'name' => 'totalVolumetricWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#VolumetricWeight',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transactionPurpose' => [
            'name' => 'transactionPurpose',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transactionPurposeCode' => [
            'name' => 'transactionPurposeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transferredFrom' => [
            'name' => 'transferredFrom',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transferredTo' => [
            'name' => 'transferredTo',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportContractId' => [
            'name' => 'transportContractId',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportContractTypeCode' => [
            'name' => 'transportContractTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LiveAnimalsEpermit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportIdentifier' => [
            'name' => 'transportIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportIndexNumeric' => [
            'name' => 'transportIndexNumeric',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#DgProductRadioactive',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportLegs' => [
            'name' => 'transportLegs',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#TransportLegs',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportMeansServiceType' => [
            'name' => 'transportMeansServiceType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/TransportMeansServiceType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportLegs',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportMeansType' => [
            'name' => 'transportMeansType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportLegs',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportMovementReference' => [
            'name' => 'transportMovementReference',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#TransportMovement',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#transportOrganization' => [
            'name' => 'transportOrganization',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Organization',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#turnable' => [
            'name' => 'turnable',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#typeCode' => [
            'name' => 'typeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#PackagingType',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#typicalCo2Coefficient' => [
            'name' => 'typicalCo2Coefficient',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#typicalFuelConsumption' => [
            'name' => 'typicalFuelConsumption',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldContourCode' => [
            'name' => 'uldContourCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ULDSpecificPiece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldLoadingIndicator' => [
            'name' => 'uldLoadingIndicator',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ULDBasicPiece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldOwnerCode' => [
            'name' => 'uldOwnerCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldRateClassType' => [
            'name' => 'uldRateClassType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldReferences' => [
            'name' => 'uldReferences',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#ULD',
            ],
            'domains' => [],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldSerialNumber' => [
            'name' => 'uldSerialNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldTareWeightForRate' => [
            'name' => 'uldTareWeightForRate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldType' => [
            'name' => 'uldType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uldTypeCode' => [
            'name' => 'uldTypeCode',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#LoadingUnit',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#unNumber' => [
            'name' => 'unNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#ProductDg',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#uniqueIdentifier' => [
            'name' => 'uniqueIdentifier',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Product',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#unit' => [
            'name' => 'unit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#unitBasis' => [
            'name' => 'unitBasis',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Ranges',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#unitPrice' => [
            'name' => 'unitPrice',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#unitsPreference' => [
            'name' => 'unitsPreference',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#UnitsPreference',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#updateBookingOptionRequests' => [
            'name' => 'updateBookingOptionRequests',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionRequest',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Booking',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#upid' => [
            'name' => 'upid',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#usedInCheck' => [
            'name' => 'usedInCheck',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#usedTemplate' => [
            'name' => 'usedTemplate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Check',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#usedToDateQuotaQuantity' => [
            'name' => 'usedToDateQuotaQuantity',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#integer',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#EpermitConsignment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#validFrom' => [
            'name' => 'validFrom',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#validUntil' => [
            'name' => 'validUntil',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#dateTime',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#valuationCharge' => [
            'name' => 'valuationCharge',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CurrencyValue',
            ],
            'domains' => [],
            'since' => '3.3',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#vatIndicator' => [
            'name' => 'vatIndicator',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#boolean',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#BillingDetails',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#vehicleModel' => [
            'name' => 'vehicleModel',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#vehicleRegistration' => [
            'name' => 'vehicleRegistration',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#vehicleSize' => [
            'name' => 'vehicleSize',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#vehicleType' => [
            'name' => 'vehicleType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#CodeListElement',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#TransportMeans',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#version' => [
            'name' => 'version',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#CheckTemplate',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#volume' => [
            'name' => 'volume',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Dimensions',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#volumeUnit' => [
            'name' => 'volumeUnit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#UnitsPreference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#volumetricWeight' => [
            'name' => 'volumetricWeight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#VolumetricWeight',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Piece',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#volumetricWeightForRate' => [
            'name' => 'volumetricWeightForRate',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#VolumetricWeight',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'since' => '3.2',
            'deprecatedIn' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#waybill' => [
            'name' => 'waybill',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Shipment',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#waybillLineItems' => [
            'name' => 'waybillLineItems',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#WaybillLineItem',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#waybillNumber' => [
            'name' => 'waybillNumber',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#waybillPrefix' => [
            'name' => 'waybillPrefix',
            'kind' => 'datatype',
            'ranges' => [
                'http://www.w3.org/2001/XMLSchema#string',
            ],
            'domains' => [
                '*',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#waybillType' => [
            'name' => 'waybillType',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#WaybillType',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#weight' => [
            'name' => 'weight',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Item',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#weightUnit' => [
            'name' => 'weightUnit',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/WeightUnitCode',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#UnitsPreference',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#weightValuationIndicator' => [
            'name' => 'weightValuationIndicator',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/code-lists/PrepaidCollectIndicator',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Waybill',
            ],
            'since' => '3.2',
            'deprecatedIn' => null,
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#width' => [
            'name' => 'width',
            'kind' => 'object',
            'ranges' => [
                'https://onerecord.iata.org/ns/cargo#Value',
            ],
            'domains' => [
                'https://onerecord.iata.org/ns/cargo#Dimensions',
            ],
            'since' => '3.2',
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
        'https://onerecord.iata.org/ns/cargo#ACCELEROMETER' => [
            'name' => 'ACCELEROMETER',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ACTIVE' => [
            'name' => 'ACTIVE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ExecutionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ACTUAL' => [
            'name' => 'ACTUAL',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ActionTimeType',
                'https://onerecord.iata.org/ns/cargo#EventTimeType',
                'https://onerecord.iata.org/ns/cargo#MovementTimeType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ALTERNATE_EMAIL_ADDRESS' => [
            'name' => 'ALTERNATE_EMAIL_ADDRESS',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ALTERNATE_PHONE_NUMBER' => [
            'name' => 'ALTERNATE_PHONE_NUMBER',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BOOKABLE' => [
            'name' => 'BOOKABLE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BOOKED' => [
            'name' => 'BOOKED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#BULK' => [
            'name' => 'BULK',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#LoadType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CANCELLED' => [
            'name' => 'CANCELLED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ExecutionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#COMPLETE' => [
            'name' => 'COMPLETE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ExecutionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#COMPOSITION' => [
            'name' => 'COMPOSITION',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#CompositionType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CONFIRMED' => [
            'name' => 'CONFIRMED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CONSIGNEE' => [
            'name' => 'CONSIGNEE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#AccountType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CONSIGNOR' => [
            'name' => 'CONSIGNOR',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#AccountType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CUSTOMER_CONTACT' => [
            'name' => 'CUSTOMER_CONTACT',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactRole',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#CUSTOMS_CONTACT' => [
            'name' => 'CUSTOMS_CONTACT',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactRole',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#DECOMPOSITION' => [
            'name' => 'DECOMPOSITION',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#CompositionType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#DELETED' => [
            'name' => 'DELETED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#DIRECT' => [
            'name' => 'DIRECT',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#WaybillType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#EMAIL_ADDRESS' => [
            'name' => 'EMAIL_ADDRESS',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#EMERGENCY_CONTACT' => [
            'name' => 'EMERGENCY_CONTACT',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactRole',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ESTIMATED' => [
            'name' => 'ESTIMATED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#EventTimeType',
                'https://onerecord.iata.org/ns/cargo#MovementTimeType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#EXPECTED' => [
            'name' => 'EXPECTED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#EventTimeType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#EXPIRED' => [
            'name' => 'EXPIRED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#FAX_NUMBER' => [
            'name' => 'FAX_NUMBER',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#FF' => [
            'name' => 'FF',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#AccountType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#GEOLOCATION' => [
            'name' => 'GEOLOCATION',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#HOUSE' => [
            'name' => 'HOUSE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#WaybillType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#HUMIDITY' => [
            'name' => 'HUMIDITY',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#INBOUND' => [
            'name' => 'INBOUND',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#DirectionType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LIGHT' => [
            'name' => 'LIGHT',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LOADING' => [
            'name' => 'LOADING',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#LoadingType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#LOOSE' => [
            'name' => 'LOOSE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#LoadType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#MAIN_CARRIAGE' => [
            'name' => 'MAIN_CARRIAGE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ModeQualifier',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#MASTER' => [
            'name' => 'MASTER',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#WaybillType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#NONBOOKABLE' => [
            'name' => 'NONBOOKABLE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#NOT_BOOKABLE' => [
            'name' => 'NOT_BOOKABLE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ON_CARRIAGE' => [
            'name' => 'ON_CARRIAGE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ModeQualifier',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#ON_REQUEST' => [
            'name' => 'ON_REQUEST',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#OUTBOUND' => [
            'name' => 'OUTBOUND',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#DirectionType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PALLET' => [
            'name' => 'PALLET',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#LoadType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PENDING' => [
            'name' => 'PENDING',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ExecutionStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PHONE_NUMBER' => [
            'name' => 'PHONE_NUMBER',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PLANNED' => [
            'name' => 'PLANNED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ActionTimeType',
                'https://onerecord.iata.org/ns/cargo#EventTimeType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PRESSURE' => [
            'name' => 'PRESSURE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#PRE_CARRIAGE' => [
            'name' => 'PRE_CARRIAGE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ModeQualifier',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#QUEUED' => [
            'name' => 'QUEUED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingOptionStatus',
                'https://onerecord.iata.org/ns/cargo#BookingStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#REJECTED' => [
            'name' => 'REJECTED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#BookingStatus',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#REQUESTED' => [
            'name' => 'REQUESTED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ActionTimeType',
                'https://onerecord.iata.org/ns/cargo#EventTimeType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#SCHEDULED' => [
            'name' => 'SCHEDULED',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#MovementTimeType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#STORE_IN' => [
            'name' => 'STORE_IN',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#StoringType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#STORE_OUT' => [
            'name' => 'STORE_OUT',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#StoringType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#TELEX' => [
            'name' => 'TELEX',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#THERMOMETER' => [
            'name' => 'THERMOMETER',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#TILT' => [
            'name' => 'TILT',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#UNIT_LOAD_DEVICE' => [
            'name' => 'UNIT_LOAD_DEVICE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#LoadType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#UNLOADING' => [
            'name' => 'UNLOADING',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#LoadingType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#UNPLANNED_STOP' => [
            'name' => 'UNPLANNED_STOP',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#DirectionType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#VIBRATION' => [
            'name' => 'VIBRATION',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#SensorType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
        'https://onerecord.iata.org/ns/cargo#WEBSITE' => [
            'name' => 'WEBSITE',
            'types' => [
                'https://onerecord.iata.org/ns/cargo#ContactDetailType',
            ],
            'since' => '3.2',
            'removedIn' => null,
        ],
    ];
}
