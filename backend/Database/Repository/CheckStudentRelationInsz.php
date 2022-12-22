<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\Input;

class CheckStudentRelationInsz extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_check_student_relation_insz", \Database\Object\CheckStudentRelationInsz::class, orderField: 'insertDateTime');
	}

	public function getByInsz($insz)
	{
		$items = $this->getNotPublished();
		return Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->childInsz, Input::formatInsz($insz))));
	}

	public function getNotPublished()
	{
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->published, false));
	}

	public function getApprovedAndNotPublished()
	{
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->locked, true) && Strings::equal($i->published, false));
	}

	public function getClassBySchool($school)
	{
		$items = $this->getNotPublished();
		$classes = Arrays::filter($items, fn ($i) => Strings::equal($i->school, $school));
		$classes = Arrays::map($classes, fn ($i) => $i->class);

		return array_unique($classes);
	}

	public function getByInstitute($institute)
	{
		$items = $this->getNotPublished();
		return array_values(Arrays::filter($items, fn ($i) => Strings::equal($i->informatInstituteNumber, $institute)));
	}
}
