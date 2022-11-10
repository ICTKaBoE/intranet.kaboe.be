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

	public function getByModule($module)
	{
		$items = $this->get(deleted: true);
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->module, $module));
		return Arrays::first($items);
	}
}
