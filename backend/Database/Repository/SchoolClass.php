<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class SchoolClass extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_school_class", \Database\Object\SchoolClass::class, orderField: 'name');
	}

	public function getBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getBySchoolIdAndClassName($schoolId, $name)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('name', $name);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
