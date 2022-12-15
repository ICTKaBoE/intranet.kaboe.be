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
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId));
	}

	public function getByModuleAndPage($moduleId, $page)
	{
		$items = $this->get(deleted: true);
		return Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->moduleId, $moduleId) && Strings::equal($i->page, $page)));
	}
}
