<?php

use Database\Object\LocalUser;
use Database\Repository\LocalUser as RepositoryLocalUser;
use Microsoft\Graph\Model\User;
use O365\Repository\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

set_time_limit(0);
require_once "./../../autoload.php";

$groupRepo = new Group;
$localUserRepo = new RepositoryLocalUser;
$group = $groupRepo->getByDisplayName('secur.intranet.kaboe.be')->doRequest()[0];

$allMembers = $groupRepo->getMembersById($group->getId())->doRequestAllPages(User::class, 300);
$allMembers = Arrays::filter($allMembers, fn ($m) => Strings::equalsIgnoreCase($m->getODataType(), "#microsoft.graph.user"));

foreach ($allMembers as $member) {
	$user = $localUserRepo->getByO365Id($member->getId());
	$user = $user ? $user : new LocalUser();

	$user->o365Id = $member->getId();
	$user->name = $member->getSurname();
	$user->firstName = $member->getGivenName();
	$user->username = $member->getMail();
	$user->jobTitle = $member->getJobTitle();
	$user->companyName = $member->getCompanyName();

	$localUserRepo->set($user);
}
