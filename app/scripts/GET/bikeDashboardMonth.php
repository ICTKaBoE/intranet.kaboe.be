<?php

use Database\Repository\BikeEvent;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Json;
use Security\Session;

require_once __DIR__ . '/../../../app/autoload.php';

$return =
    [
        'title' => 'Gereden deze maand'
    ];
$start = date('Y-m-d', strtotime('first day of this month'));
$end = date('Y-m-d', strtotime('last day of this month'));

$bikeEvents = (new BikeEvent)->getByUpn(Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn']);
$bikeEvents = Arrays::filter($bikeEvents, fn ($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));
$formattedBikeEvents = [];

if ($bikeEvents) {
    foreach ($bikeEvents as $bikeEvent) {
        $formattedBikeEvents[] = [
            'x' => $bikeEvent->date,
            'y' => $bikeEvent->distanceInKm
        ];
    }

    $return['series'] = $formattedBikeEvents;
}

echo Json::safeEncode($return);
