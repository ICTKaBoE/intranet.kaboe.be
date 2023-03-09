<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class SchoolInstitute extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_school_institute", \Database\Object\SchoolInstitute::class, orderField: false);
	}

	public function getBySchoolId($schoolId)
	{
		$items = $this->get();
		return array_values(Arrays::filter($items, fn ($i) => Strings::equal($i->schoolId, $schoolId)));
	}
}
