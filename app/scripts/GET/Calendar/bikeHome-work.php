<?php

use Database\Repository\BikeEventHomeWork;
use Database\Repository\LocalUser;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Json;
use Security\User;

include_once __DIR__ . "/../../../autoload.php";

$return = [];

$start = Clock::at($_GET['start']);
$end = Clock::at($_GET['end']);

$user = (new LocalUser)->get(User::getLoggedInUser()->id)[0];
$events = (new BikeEventHomeWork)->getByUserId($user->id);
$events = Arrays::filter($events, fn ($e) => $e->distance > 0);

foreach ($events as $event) {
	$return[] = [
		"start" => $event->date,
		"title" => round($event->distance, 2) . " km",
		"display" => "background",
		"backgroundColor" => $event->userHomeWorkDistance->color,
		"borderColor" => $event->userHomeWorkDistance->borderColor,
		"textColor" => $event->userHomeWorkDistance->textColor,
		"allDay" => true,
	];
}


echo Json::safeEncode($return);
