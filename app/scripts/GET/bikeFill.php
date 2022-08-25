<?php

use Core\Config;
use Database\Repository\BikeEvent;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Json;
use Security\Session;

require_once __DIR__ . '/../../../app/autoload.php';

$start = Arrays::first(explode("T", $_GET['start']));
$end = Arrays::first(explode("T", $_GET['end']));

$bikeEvents = (new BikeEvent)->getByUpn(Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn']);
$bikeEvents = Arrays::filter($bikeEvents, fn ($e) => $e->distance > 0 && $e->distanceInKm > 0);
$formattedBikeEvents = [];

if ($bikeEvents) {
    foreach ($bikeEvents as $bikeEvent) {
        $formattedBikeEvents[] = [
            'id' => $bikeEvent->id,
            'start' => $bikeEvent->date,
            'allDay' => true,
            'overlap' => true,
            'display' => 'background',
            'backgroundColor' => Config::get("tool/bike/colorDistance{$bikeEvent->distance}"),
        ];
    }
}

echo Json::safeEncode($formattedBikeEvents);
