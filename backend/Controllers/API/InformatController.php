<?php

namespace Controllers\API;

use Controllers\ApiController;
use Informat\Repository\Student;

class InformatController extends ApiController
{
	public function student($id = null)
	{
		$items = (new Student)->get($id);

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function studentByInstituteByRRN($institute = null, $rrn = null)
	{
		if (is_null($institute)) {
			$this->setHttpCode(400);
			$this->appendToJson("error", "Institute must be filled in!");
		} else if (is_null($rrn)) {
			$this->setHttpCode(400);
			$this->appendToJson("error", "RRN must be filled in!");
		} else {
			$studentRepo = new Student;
			$studentRepo->setInstituteNumber($institute);
			$items = $studentRepo->getByRRN($rrn);

			$this->appendToJson("items", $items);
		}

		$this->handle();
	}
}
