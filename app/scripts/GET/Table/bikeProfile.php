<?php

use Database\Repository\UserHomeWorkDistance;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;
use Security\User;

include_once __DIR__ . "/../../../autoload.php";

$return = [];
Arrays::setNestedValue($return, ["columns"], [
	[
		"type" => "checkbox",
		"class" => ["w-1"],
		"data" => "id"
	],
	[
		"title" => "Alias",
		"data" => "alias"
	],
	[
		"title" => "Eindbestemming",
		"data" => "endSchool.name"
	],
	[
		"type" => "double",
		"title" => "Afstand",
		"data" => "distance",
		"suffix" => "km"
	]
]);

Arrays::setNestedValue($return, ["format", "row"], [
	"backgroundColorValue" => "color",
	"textColorValue" => "textColor"
]);

$distances = (new UserHomeWorkDistance)->getByUserId(User::getLoggedInUser()->id);
Arrays::setNestedValue($return, ["rows"], $distances);

echo Json::safeEncode($return);
