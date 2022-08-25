<?php

use Ouzo\Utilities\Json;
use Security\Session;

require_once __DIR__ . '/../../../app/autoload.php';

$return = [];

$pageErrorSessionValue = Session::get(SECURITY_SESSION_PAGEERROR);
if (!is_null($pageErrorSessionValue)) $return['errors'][] = $pageErrorSessionValue;

echo Json::safeEncode($return);
