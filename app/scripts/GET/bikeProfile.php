<?php

use Database\Repository\BikeProfile;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;
use Security\Session;

require_once __DIR__ . '/../../../app/autoload.php';

$return = [];

$bikeProfile = (new BikeProfile)->getByUpn(Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn']);
if (!is_null($bikeProfile)) Arrays::setNestedValue($return, ['fields'], $bikeProfile->toArray());

echo Json::safeEncode($return);
