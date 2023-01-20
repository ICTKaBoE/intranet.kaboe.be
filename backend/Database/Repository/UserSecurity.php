<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class UserSecurity extends Repository
{
	private $rightsOrder = [
		1 => 'view',
		2 => 'edit',
		3 => 'export',
		4 => 'changeSettings'
	];

	public function __construct()
	{
		parent::__construct("tbl_user_security", \Database\Object\UserSecurity::class, orderField: 'moduleId');
	}

	public function getByModuleId($moduleId)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId));
		return $items;
	}

	public function getByUserAndModule($userId, $moduleId)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId) && Strings::equal($i->userId, $userId));
		return Arrays::firstOrNull($items);
	}

	public function hasPermissionToEnter($moduleId, $userId)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId));
		$items = array_values($items);

		if ($items[0]->moduleId === 0 && $items[0]->view) return true;

		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId));
		$items = array_values($items);
		if (count($items) !== 0 && $items[0]->view) return true;

		return false;
	}

	public function hasPermissionToEnterSub($navItem, $moduleId, $userId)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId));
		$items = array_values($items);

		if ($items[0]->moduleId === 0) return true;
		else {
			$permissions = Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId)));

			if (is_null($permissions)) return false;
			else {
				$hasPermission = false;

				foreach ($this->rightsOrder as $index => $value) {
					if (Strings::equalsIgnoreCase($navItem->minimumRights, $value)) {
						if ($permissions->$value) {
							$hasPermission = true;
							break;
						}
					}
				}

				return $hasPermission;
			}
		}
	}
}
