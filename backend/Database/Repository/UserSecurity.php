<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class UserSecurity extends Repository
{
	public static $rightsOrder = [
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

	public function getByUserId($userId)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId));
		return array_values(Arrays::orderBy($items, 'moduleId'));
	}

	public function getByUserAndModule($userId, $moduleId)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId) && Strings::equal($i->userId, $userId));
		return Arrays::firstOrNull($items);
	}
}
