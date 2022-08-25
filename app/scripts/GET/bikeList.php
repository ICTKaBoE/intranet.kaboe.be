<?php

use Ouzo\Utilities\Arrays;
use Database\Repository\BikeEvent;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [
    'header' => [
        [
            'value' => 'date',
            'text' => 'Datum',
            'width' => 100
        ],
        [
            'value' => 'distanceInKm',
            'text' => 'Afstand'
        ]
    ]
];

$upn = $_GET['upn'] ?? 0;

$bikeEvents = (new BikeEvent)->get();
$bikeEvents = Arrays::filter($bikeEvents, fn ($be) => Strings::equalsIgnoreCase($upn, $be->upn) && $be->distance != 0);

Arrays::setNestedValue($return, ['rows'], $upn > 0 ? Arrays::orderBy($bikeEvents, 'date') : []);

echo Json::safeEncode($return);
