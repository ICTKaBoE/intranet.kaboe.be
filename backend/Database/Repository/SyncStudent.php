<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;
use Database\Repository\SchoolInstitute;
use Ouzo\Utilities\Strings;

class SyncStudent extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_sync_student", \Database\Object\SyncStudent::class, orderField: "name");
	}

	public function getByInformatUID($informatUID)
	{
		$statement = $this->prepareSelect();
		$statement->where("informatUID", $informatUID);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getBySchool($schoolId)
	{
		$institutes = (new SchoolInstitute)->getBySchoolId($schoolId);

		$statement = $this->prepareSelect();
		$statement->whereIn("instituteId", Arrays::map($institutes, fn ($i) => $i->id));

		return $this->executeSelect($statement);
	}

	public function getClassBySchool($school)
	{
		$institutes = (new SchoolInstitute)->getBySchoolId($school);

		$statement = $this->prepareSelect();
		$statement->whereIn("instituteId", Arrays::map($institutes, fn ($i) => $i->id));

		$items = $this->executeSelect($statement);
		$classes = Arrays::map($items, fn ($i) => $i->class);

		return array_unique($classes);
	}
}
