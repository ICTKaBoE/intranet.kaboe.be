<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class School extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_school", \Database\Object\School::class, orderField: 'name');
	}

	public function getByName($name)
	{
		$items = $this->get();
		return Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->name, $name)));
	}
}
