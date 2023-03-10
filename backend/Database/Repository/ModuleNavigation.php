<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class ModuleNavigation extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_module_navigation", \Database\Object\ModuleNavigation::class);
	}

	public function getByModuleId($moduleId)
	{
		$statement = $this->prepareSelect();
		$statement->where('moduleId', $moduleId);

		return $this->executeSelect($statement);
	}

	public function getByModuleAndPage($moduleId, $page)
	{
		$statement = $this->prepareSelect(deleted: true);
		$statement->where('moduleId', $moduleId);
		$statement->where('page', $page);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
