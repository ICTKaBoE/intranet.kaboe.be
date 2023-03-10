<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class Module extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_module", \Database\Object\Module::class);
	}

	public function getByScope($scope)
	{
		$statement = $this->prepareSelect();
		$statement->where('scope', 'LIKE', "%$scope%");

		return $this->executeSelect($statement);
	}

	public function getByModule($module)
	{
		$statement = $this->prepareSelect(deleted: true);
		$statement->where('module', $module);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getWhereAssignUserRights()
	{
		$statement = $this->prepareSelect();
		$statement->where('assignUserRights', 1);

		return $this->executeSelect($statement);
	}
}
