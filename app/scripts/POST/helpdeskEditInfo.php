<?php

use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;
use Database\Repository\Helpdesk;
use Database\Repository\HelpdeskAction;
use Database\Object\HelpdeskAction as ObjectHelpdeskAction;
use O365\Objects\User;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];
$continue = true;

$id = $_POST['helpdeskId'];
$assignedToUpn = $_POST['assignedToUpn'];
$priority = $_POST['priority'];

$helpdeskRepo = new Helpdesk;
$helpdeskActionRepo = new HelpdeskAction;

$helpdesk = $helpdeskRepo->get($id)[0];
$info = "";

if ($assignedToUpn && !Strings::equalsIgnoreCase($helpdesk->assignedToUpn, $assignedToUpn)) {
    $info .= "Assigned to {$assignedToUpn}";
    $helpdesk->assignedToUpn = $assignedToUpn;
    $helpdesk->assignedToName = (new User)->get($assignedToUpn)->getDisplayName();
}

if ($priority && !Strings::equalsIgnoreCase($helpdesk->priority, $priority)) {
    $info .= (Strings::isNotBlank($info) ? "<br />" : "") . "Changed priority to {$priority}";
    $helpdesk->priority = $priority;
}

$helpdeskActionId = $helpdeskActionRepo->set(new ObjectHelpdeskAction([
    'helpdeskId' => $helpdesk->id,
    'type' => 'UPDATE',
    'info' => $info
]));

$helpdesk->lastActionTimestamp = $helpdeskActionRepo->get($helpdeskActionId)[0]->timestamp;

$helpdeskRepo->set($helpdesk);

echo Json::safeEncode($return);
