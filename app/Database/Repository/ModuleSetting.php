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
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId));
	}
}
