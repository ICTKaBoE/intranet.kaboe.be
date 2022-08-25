<?php

use Security\Request;
use Ouzo\Utilities\Json;
use Database\Repository\Helpdesk;
use Database\Repository\HelpdeskMessage;

require_once __DIR__ . '/../../../app/autoload.php';

$return = [];

$helpdesk = (new Helpdesk)->get(Request::parameter(REQUEST_ROUTE_PARAMETER_ID))[0];
$messages = (new HelpdeskMessage)->getByHelpdeskId($helpdesk->id);

$return['items'] = $messages;

echo Json::safeEncode($return);
