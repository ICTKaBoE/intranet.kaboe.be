<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\School;
use Database\Repository\SchoolClass;
use Database\Repository\SchoolInstitute;
use Router\Helpers;

class SchoolController extends ApiController
{
	public function school($view, $schoolId = null)
	{
		$schools = (new School)->get($schoolId);
		if ($view == "select") $this->appendToJson(['items'], $schools);
		$this->handle();
	}

	public function institute($view, $instituteId = null)
	{
		$institutes = (new SchoolInstitute)->get($instituteId);
		if ($view == "select") $this->appendToJson(['items'], $institutes);
		$this->handle();
	}

	public function class($view, $classId = null)
	{
		$schoolId = Helpers::input()->get("parentValue", Helpers::input()->get("schoolId"))?->getValue();

		$classes = is_null($schoolId) ? [] : (new SchoolClass)->getBySchoolId($schoolId);
		if ($view == "select") $this->appendToJson(['items'], $classes);
		$this->handle();
	}
}
