<?php

use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;
use Database\Repository\Helpdesk;
use Database\Repository\HelpdeskAction;
use Database\Object\HelpdeskAction as ObjectHelpdeskAction;
use Database\Object\HelpdeskMessage as ObjectHelpdeskMessage;
use Database\Repository\HelpdeskMessage;
use O365\Objects\User;
use Ouzo\Utilities\Clock;
use Security\Session;
use Security\User as SecurityUser;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [
    'reset' => true
];
$continue = true;

$id = $_POST['helpdeskId'];
$message = $_POST['message'];

$helpdeskRepo = new Helpdesk;
$helpdeskActionRepo = new HelpdeskAction;
$helpdeskMessageRepo = new HelpdeskMessage;

$helpdesk = $helpdeskRepo->get($id)[0];
$helpdesk->status = 'OPEN';

$helpdeskMessageRepo->set(new ObjectHelpdeskMessage([
    'helpdeskId' => $helpdesk->id,
    'message' => $message,
    'upn' => Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn'],
    'name' => SecurityUser::get()->getDisplayName()
]));

$helpdeskActionId = $helpdeskActionRepo->set(new ObjectHelpdeskAction([
    'helpdeskId' => $helpdesk->id,
    'type' => 'UPDATE',
    'info' => Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn'] . " answered ticket"
]));

$helpdesk->lastActionTimestamp = $helpdeskActionRepo->get($helpdeskActionId)[0]->timestamp;

$helpdeskRepo->set($helpdesk);

echo Json::safeEncode($return);
