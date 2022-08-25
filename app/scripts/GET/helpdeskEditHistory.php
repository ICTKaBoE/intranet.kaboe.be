<?php

use Security\Request;
use Ouzo\Utilities\Json;
use Database\Repository\Helpdesk;
use Database\Repository\HelpdeskAction;

require_once __DIR__ . '/../../../app/autoload.php';

$return = [];

$helpdesk = (new Helpdesk)->get(Request::parameter(REQUEST_ROUTE_PARAMETER_ID))[0];
$actions = (new HelpdeskAction)->getByHelpdeskId($helpdesk->id);

$return['items'] = $actions;

echo Json::safeEncode($return);
