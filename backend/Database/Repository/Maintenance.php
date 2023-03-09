<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class Maintenance extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_maintenance", \Database\Object\Maintenance::class, orderField: 'lastActionDateTime', orderDirection: 'DESC');
	}

	public function getBySchoolId($schoolId)
	{
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->schoolId, $schoolId));
	}
}
