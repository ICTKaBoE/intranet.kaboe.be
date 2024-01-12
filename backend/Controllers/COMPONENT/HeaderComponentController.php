<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Router\Helpers;
use Security\User;

class HeaderComponentController extends ComponentController
{
	public function __construct()
	{
		parent::__construct('header');
		$this->writeClasses();
	}

	private function writeClasses()
	{
		$this->layout = str_replace("{{is:public:display}}", Helpers::isPublicPage() ? 'd-none' : '', $this->layout);
	}

	// Loader

	private function loadUserDetails()
	{
		$details = $this->getUserDetails();

		foreach ($details->toArray() as $key => $value) $this->layout = str_replace("{{user:" . $key . "}}", $value, $this->layout);
	}

	// Getters

	private function getUserDetails()
	{
		return User::getLoggedInUser();
	}
}
