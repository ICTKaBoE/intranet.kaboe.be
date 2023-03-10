<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Setting extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_setting", \Database\Object\Setting::class, orderField: 'order');
	}

	public function getByTabId($tabId)
	{
		$statement = $this->prepareSelect();
		$statement->where('settingTabId', $tabId);

		return $this->executeSelect($statement);
	}
}
