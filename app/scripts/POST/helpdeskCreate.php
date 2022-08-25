<?php

use Core\Config;
use Security\User;
use Security\Session;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Object\Helpdesk;
use Database\Object\HelpdeskAction;
use Database\Object\HelpdeskMessage;
use Database\Repository\Helpdesk as RepositoryHelpdesk;
use Database\Repository\HelpdeskAction as RepositoryHelpdeskAction;
use Database\Repository\HelpdeskMessage as RepositoryHelpdeskMessage;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [
    'reset' => true
];
$continue = true;

$priority = $_POST['priority'];
$schoolId = $_POST['schoolId'];
$type = $_POST['type'];
$deviceName = $_POST['deviceName'];
$subject = $_POST['subject'];
$message = $_POST['message'];

if (Strings::isBlank($priority)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'priority', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'priority', 'feedback'], "Prioriteit is verplicht!");
}

if (Strings::isBlank($schoolId)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'schoolId', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'schoolId', 'feedback'], "School is verplicht!");
}

if (Strings::isBlank($type)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'type', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'type', 'feedback'], "Type is verplicht!");
}

if (Strings::isBlank($deviceName)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'deviceName', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'deviceName', 'feedback'], "Toestelnaam is verplicht!");
}

if (Strings::isBlank($subject)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'subject', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'subject', 'feedback'], "Onderwerp is verplicht!");
}

if (Strings::isBlank($message)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'message', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'message', 'feedback'], "Bericht is verplicht!");
}

if ($continue) {
    try {
        $user = User::get();

        $helpdesk = new Helpdesk([
            'priority' => $priority,
            'schoolId' => $schoolId,
            'schoolName' => Config::get("schools/{$schoolId}"),
            'creatorUpn' => Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn'],
            'creatorName' => $user->getDisplayName(),
            'type' => $type,
            'deviceName' => $deviceName,
            'subject' => $subject,
        ]);

        $helpdeskId = (new RepositoryHelpdesk)->set($helpdesk);

        $helpdeskMessage = new HelpdeskMessage([
            'helpdeskId' => $helpdeskId,
            'message' => $message,
            "upn" => Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn'],
            "name" => $user->getDisplayName()
        ]);

        (new RepositoryHelpdeskMessage)->set($helpdeskMessage);

        $helpdeskAction = new HelpdeskAction([
            'helpdeskId' => $helpdeskId,
            'type' => 'CREATE',
            'info' => 'Created by ' . Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn'] . " ({$user->getDisplayName()})"
        ]);

        (new RepositoryHelpdeskAction)->set($helpdeskAction);

        $return['message']['state'] = "success";
        $return['message']['content'] = "Ticket aangemaakt!";
        $return['message']['disappear'] = 5;
    } catch (\Exception $e) {
        $return['error'] = $e->getMessage();
    }
}

echo Json::safeEncode($return);
