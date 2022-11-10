<?php

use Database\Object\BikeEventHomeWork as ObjectBikeEventHomeWork;
use Security\User;
use Router\Helpers;
use Ouzo\Utilities\Strings;
use Database\Repository\BikeEventHomeWork;
use Database\Repository\UserHomeWorkDistance;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;

include_once "../../autoload.php";

$return = [];
$continue = true;

$url = ltrim(rtrim(Helpers::url()->getRelativeUrl(), "/"), "/");
$id = Strings::underscoreToCamelCase(str_replace("/", "_", $url));

$date = $_POST['date'];

$bikeEventHomeWorkRepo = new BikeEventHomeWork;
$existingBikeEvent = $bikeEventHomeWorkRepo->getByIdAndDate(User::getLoggedInUser()->id, $date);
$userHomeWorkDistances = Arrays::orderBy((new UserHomeWorkDistance)->getByUserId(User::getLoggedInUser()->id), 'id');

if (is_null($existingBikeEvent)) {
	$userHomeWorkDistances = Arrays::first($userHomeWorkDistances);

	$existingBikeEvent = new ObjectBikeEventHomeWork([
		'userId' => User::getLoggedInUser()->id,
		'userAddressId' => $userHomeWorkDistances->startAddressId,
		'userHomeWorkDistanceId' => $userHomeWorkDistances->id,
		'date' => $date,
		'distance' => $userHomeWorkDistances->distance
	]);
} else {
	$nextUserHomeWorkDistance = $userHomeWorkDistances[0];

	foreach ($userHomeWorkDistances as $index => $uhwd) {
		if (Strings::equal($uhwd->id, $existingBikeEvent->userHomeWorkDistanceId)) {
			$nextUserHomeWorkDistance = $userHomeWorkDistances[$index + 1];
			// if (is_null($nextUserHomeWorkDistance)) $nextUserHomeWorkDistance = $userHomeWorkDistances[0];
			break;
		}
	}

	$existingBikeEvent->userAddressId = $nextUserHomeWorkDistance->startAddressId;
	$existingBikeEvent->userHomeWorkDistanceId = $nextUserHomeWorkDistance->id;
	$existingBikeEvent->distance = $nextUserHomeWorkDistance->distance;
}

$bikeEventHomeWorkRepo->set($existingBikeEvent);

$return['reload'] = true;
echo Json::safeEncode($return);
