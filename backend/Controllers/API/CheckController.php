<?php

namespace Controllers\API;

use Controllers\ApiController;
use Router\Helpers;

class CheckController extends ApiController
{
	public function getStudents($view)
	{
		$schoolId = Helpers::input()->get('schoolId')?->getValue();
		$classId = Helpers::input()->get('classId')?->getValue();

		$this->handle();
	}
}
