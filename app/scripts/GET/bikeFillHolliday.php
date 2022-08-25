<?php

use Database\Repository\Holliday;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;

require_once __DIR__ . '/../../../app/autoload.php';

$start = Arrays::first(explode("T", $_GET['start']));
$end = Arrays::first(explode("T", $_GET['end']));

$hollidays = (new Holliday)->get();
$formattedHollidays = [];

if ($hollidays) {
    foreach ($hollidays as $holliday) {
        $formattedHollidays[] = [
            'id' => $holliday->id,
            'title' => $holliday->name,
            'start' => $holliday->start,
            'end' => $holliday->end,
            'allDay' => $holliday->fullDay,
        ];
    }
}

echo Json::safeEncode($formattedHollidays);
