<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Security\Input;

class CheckStudentRelationInsz extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_check_student_relation_insz", \Database\Object\CheckStudentRelationInsz::class, orderField: 'insertDateTime');
	}

	public function getByInsz($insz)
	{
		$statement = $this->prepareSelect();
		$statement->where('childInsz', Input::formatInsz($insz));

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getByCheckField($insz)
	{
		$statement = $this->prepareSelect();
		$statement->where('checkField', $insz);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getNotPublished()
	{
		$statement = $this->prepareSelect();
		$statement->where('published', false);

		return $this->executeSelect($statement);
	}

	public function getApprovedAndNotPublished()
	{
		$statement = $this->prepareSelect();
		$statement->where('locked', true);
		$statement->where('published', false);

		return $this->executeSelect($statement);
	}

	public function getClassBySchool($school)
	{
		$school = (new School)->get($school)[0]->name;

		$statement = $this->prepareSelect();
		$statement->where('school', $school);

		$items = $this->executeSelect($statement);
		$classes = Arrays::map($items, fn ($i) => $i->class);

		return array_unique($classes);
	}

	public function getByInstitute($institute)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatInstituteNumber', $institute);

		return $this->executeSelect($statement);
	}

	public function getBySchoolName($schoolName)
	{
		$statement = $this->prepareSelect();
		$statement->where('school', $schoolName);

		return $this->executeSelect($statement);
	}
}
