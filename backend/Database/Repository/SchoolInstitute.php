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
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getByInstituteNumber($number)
	{
		$statement = $this->prepareSelect();
		$statement->where('instituteNumber', $number);

		return $this->executeSelect($statement);
	}
}
