<?php

use Core\Config;
use Security\Session;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;
use Ouzo\Utilities\Clock;
use Database\Repository\BikeEvent;
use Database\Repository\BikeProfile;
use Database\Object\BikeEvent as ObjectBikeEvent;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [
    'reload' => true
];
$date = $_POST['date'];
$upn = Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn'];

$blockPastRegistrations = (Strings::equal(Config::get("tool/bike/blockPastRegistration"), true) || Strings::equal(Config::get("tool/bike/blockPastRegistration"), 'on'));
$blockPast = ($blockPastRegistrations) ? Clock::now()->minusMonths(2) : false;

try {
    if (!$blockPastRegistrations || (!is_bool($blockPast) && $blockPast->isBefore(Clock::at($date)))) {
        $bikeEventRepo = new BikeEvent;
        $eventOnDate = $bikeEventRepo->getByDateAndUpn($date, $upn);
        $bikeProfile = (new BikeProfile)->getByUpn($upn);

        if (is_null($eventOnDate)) {
            $eventBikeOptions = [
                'date' => $date,
                'upn' => $upn,
                'distanceInKm' => $bikeProfile->distance1
            ];

            $bikeEventRepo->set(new ObjectBikeEvent($eventBikeOptions));
        } else {
            if ($eventOnDate->distance == 0 && $bikeProfile->distance1 >= 1) $eventOnDate->distance = 1;
            else if ($eventOnDate->distance == 1  && $bikeProfile->distance2 >= 1) $eventOnDate->distance = 2;
            else if ($eventOnDate) $eventOnDate->distance = 0;

            $eventOnDate->distanceInKm = ($eventOnDate->distance == 1 ? $bikeProfile->distance1 : ($eventOnDate->distance == 2 ? $bikeProfile->distance2 : 0));
            $bikeEventRepo->set($eventOnDate);
        }
    }
} catch (\Exception $e) {
    $return['error'] = $e->getMessage();
}

echo Json::safeEncode($return);
