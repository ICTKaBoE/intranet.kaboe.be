<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Database\Repository\School;
use Router\Helpers;
use Security\User;

class SchoolHeaderComponentController extends ComponentController
{
	public function __construct()
	{
		parent::__construct('schoolheader');
		$this->loadSchoolDetails();
	}

	// Loader

	private function loadSchoolDetails()
	{
		$details = (new School)->get(Helpers::url()->getParam('schoolId'))[0];

		foreach ($details->toArray() as $key => $value) $this->layout = str_replace("{{school:" . $key . "}}", $value, $this->layout);
	}
}
