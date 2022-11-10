<?php

use Database\Object\ToolPermission as ObjectToolPermission;
use Database\Repository\Tool;
use O365\Objects\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\ToolPermission;
use Microsoft\Graph\Model\User as ModelUser;

set_time_limit(0);

require_once __DIR__ . "/../../autoload.php";

$groupRepo = new Group;
$tools = (new Tool)->get();
$tools = Arrays::filter($tools, fn ($t) => $t->showInSettings);
$toolPermissionRepo = new ToolPermission;
$group = $groupRepo->getByDisplayName('secur.intranet.kaboe.be')->doRequest();

$allMembers = $groupRepo->getMembersById($group[0]->getId())->doRequestAllPages(ModelUser::class, 300);
$allMembers = Arrays::filter($allMembers, fn ($m) => Strings::equalsIgnoreCase($m->getODataType(), "#microsoft.graph.user"));

foreach ($allMembers as $member) {
    foreach ($tools as $tool) {
        $memberObject = [
            'id' => null,
            'toolId' => $tool->id,
            'upn' => $member->getMail()
        ];
        $memberObject = new ObjectToolPermission($memberObject);

        try {
            $toolPermissionRepo->set($memberObject);
        } catch (\Exception $e) {
            die(var_dump($e->getMessage()));
        }
    }
}
