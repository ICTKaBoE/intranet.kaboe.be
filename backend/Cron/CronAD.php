<?php

namespace Cron;

use Database\Object\LocalUser as ObjectLocalUser;
use Database\Object\UserSecurity as ObjectUserSecurity;
use O365\Repository\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\LocalUser;
use Database\Repository\UserSecurity;

abstract class CronAD
{
	public static function Sync()
	{
		$groupRepo = new Group;
		$localUserRepo = new LocalUser;
		$userSecurityRepo = new UserSecurity;
		$group = $groupRepo->getByDisplayName('secur.intranet.kaboe.be')->doRequest()[0];
		$modules = (new Module)->getWhereAssignUserRights();

		$allMembers = $groupRepo->getMembersByGroupId($group->getId())->doRequestAllPages(\Microsoft\Graph\Model\User::class, 300);
		$allMembers = Arrays::filter($allMembers, fn ($m) => Strings::equalsIgnoreCase($m->getODataType(), "#microsoft.graph.user"));

		foreach ($allMembers as $member) {
			try {
				$user = $localUserRepo->getByO365Id($member->getId()) ?? new ObjectLocalUser;

				$user->o365Id = $member->getId();
				$user->name = $member->getSurname();
				$user->firstName = $member->getGivenName();
				$user->username = $member->getMail();
				$user->jobTitle = $member->getJobTitle();
				$user->companyName = $member->getCompanyName();
				$user->active = $member->getAccountEnabled();

				$userId = $localUserRepo->set($user);

				if (is_null($user->id)) $user = $localUserRepo->get($userId);
				else $userId = $user->id;

				foreach ($modules as $module) {
					$rights = str_split($module->defaultRights);
					if (Strings::equal($rights[0], 0)) continue;

					$userSecurity = $userSecurityRepo->getByUserAndModule($userId, $module->id) ?? new ObjectUserSecurity([
						'moduleId' => $module->id,
						'userId' => $userId,
						'view' => $rights[0],
						'edit' => $rights[1],
						'export' => $rights[2],
						'changeSettings' => $rights[3]
					]);

					try {
						$userSecurityRepo->set($userSecurity);
					} catch (\Exception $e) {
						die(var_dump("Security update: " . $e->getMessage()));
					}
				}
			} catch (\Exception $e) {
				die(var_dump("Member update: " . $e->getMessage()));
			}
		}
	}
}
