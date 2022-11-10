<?php

use Database\Repository\Holliday;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Json;

include_once __DIR__ . "/../../../autoload.php";

$return = [];

$start = Clock::at($_GET['start']);
$end = Clock::at($_GET['end']);

$events = (new Holliday)->get();

foreach ($events as $event) {
	$return[] = [
		"id" => $event->id,
		"start" => $event->start,
		"end" => $event->end,
		"title" => ($event->school ? $event->school->name . ": " : "") . $event->name,
		"allDay" => $event->fullDay
	];
}


echo Json::safeEncode($return);
