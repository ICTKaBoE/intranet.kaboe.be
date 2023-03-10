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
		$statement = $this->prepareSelect();
		$statement->where('moduleId', $moduleId);

		return $this->executeSelect($statement);
	}

	public function getByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);

		return Arrays::orderBy($this->executeSelect($statement), 'moduleId');
	}

	public function getByUserAndModule($userId, $moduleId)
	{
		$statement = $this->prepareSelect();
		$statement->where('moduleId', $moduleId);
		$statement->where('userId', $userId);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
