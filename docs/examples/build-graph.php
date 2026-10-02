<?php

declare(strict_types=1);

use LambdaTwelve\OneRecord\Model\Builder\Embedded;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LocalGraph;
use LambdaTwelve\OneRecord\Model\UuidIriMinter;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;

// A master air waybill with its shipment, one piece and the shipper, linked by
// local keys. Every property is checked against the ontology as it is set.
$graph = LocalGraph::create()
    ->add('waybill', ObjectBuilder::of(Cargo::Waybill)
        ->set(Cargo::waybillPrefix, '020')
        ->set(Cargo::waybillNumber, '12345675')
        ->set(Cargo::waybillType, Values::individual(Cargo::MASTER))
        ->set(Cargo::shipment, Values::ref('shipment')))
    ->add('shipment', ObjectBuilder::of(Cargo::Shipment)
        ->set(Cargo::goodsDescription, 'Machine parts')
        ->set(Cargo::totalGrossWeight, Values::quantity(190.5, MeasurementUnitCode::KGM))
        ->set(Cargo::waybill, Values::ref('waybill'))
        ->add(Cargo::pieces, Values::ref('piece-1'))
        ->add(Cargo::involvedParties, Embedded::of(Cargo::Party)
            ->set(Cargo::partyRole, Values::code('ParticipantIdentifier', 'SHP'))
            ->set(Cargo::partyDetails, Values::ref('shipper'))))
    ->add('piece-1', ObjectBuilder::of(Cargo::Piece)
        ->set(Cargo::grossWeight, Values::quantity(190.5, MeasurementUnitCode::KGM))
        ->set(Cargo::coload, false))
    ->add('shipper', ObjectBuilder::of(Cargo::Company)
        ->set(Cargo::name, 'ACME Machines'));

// Mint URIs under the host's base URL; the same seed always yields the same URIs.
$resolved = $graph->resolve(new UuidIriMinter('https://1r.example.com', seed: 'shipment-AER-1'));

echo $resolved->root()->toJson();
