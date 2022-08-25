<?php

use Security\Request;
use O365\Objects\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Tool;
use Microsoft\Graph\Model\User;
use Database\Repository\ToolPermission;
use Ouzo\Utilities\Json;

require_once __DIR__ . '/../../../app/autoload.php';

$return = [];

$tool = (new Tool)->getByRoute(Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL));
$toolPermissions = (new ToolPermission)->getByToolId($tool->id);
$toolPermissions = array_values(Arrays::filter($toolPermissions, fn ($t) => $t->react == 1));

$groupRepo = new Group;
$group = $groupRepo->getByDisplayName('secur.intranet.kaboe.be')->doRequest();

$allMembers = $groupRepo->getMembersById($group[0]->getId())->doRequestAllPages(User::class, 300);
$allMembers = Arrays::filter($allMembers, fn ($m) => Strings::equalsIgnoreCase($m->getODataType(), "#microsoft.graph.user") && Arrays::contains(Arrays::map($toolPermissions, fn ($tp) => $tp->upn), $m->getMail()));
usort($allMembers, fn ($a, $b) => strcmp($a->getMail(), $b->getMail()));

$return['items'] = $allMembers;

echo Json::safeEncode($return);
