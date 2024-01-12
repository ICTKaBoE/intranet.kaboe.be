<?php

namespace Controllers\API;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Controllers\ApiController;
use Database\Repository\InformatStaff;
use Database\Repository\InformatStudent;
use Database\Repository\SchoolInstitute;
use Database\Repository\InformatStaffAssignment;

class InformatController extends ApiController
{
	// GET
	public function getStudents($view)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue")?->getValue();
			$institutes = (new SchoolInstitute)->getBySchoolId($schoolId);

			$students = [];
			foreach ($institutes as $institute) {
				$_students = (new InformatStudent)->getByInstitute($institute->instituteNumber);
				$students = Arrays::concat([$students, $_students]);
			}

			$this->appendToJson("items", $students);
		}

		$this->handle();
	}

	public function getStaff($view)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue")?->getValue();
			$institutes = (new SchoolInstitute)->getBySchoolId($schoolId);

			$staff = [];
			foreach ($institutes as $institute) {
				$_staffAssignment = (new InformatStaffAssignment)->getByInstituteId($institute->instituteNumber);
				$_staffAssignment = Arrays::uniqueBy($_staffAssignment, "informatStaffUID");

				foreach ($_staffAssignment as $_staffA) {
					$staff[] = (new InformatStaff)->getByInformatUID($_staffA->informatStaffUID);
				}
			}

			$this->appendToJson("items", $staff);
		}

		$this->handle();
	}
}
