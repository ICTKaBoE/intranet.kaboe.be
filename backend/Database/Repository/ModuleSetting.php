<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class ModuleSetting extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_module_setting", \Database\Object\ModuleSetting::class, orderField: false);
	}

	public function getByModule($moduleId)
	{
		$statement = $this->prepareSelect();
		$statement->where('moduleId', $moduleId);

		return $this->executeSelect($statement);
	}

	public function getByModuleAndKey($moduleId, $key): \Database\Object\ModuleSetting
	{
		$statement = $this->prepareSelect();
		$statement->where('moduleId', $moduleId);
		$statement->where('key', $key);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
