<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Security\User;

class HeaderComponentController extends ComponentController
{
	public function __construct()
	{
		parent::__construct('header');
		$this->loadUserDetails();
	}

	// Loader

	private function loadUserDetails()
	{
		$details = $this->getUserDetails();

		foreach ($details as $key => $value) $this->layout = str_replace("{{user:" . $key . "}}", $value, $this->layout);
	}

	// Getters

	private function getUserDetails()
	{
		return User::getLoggedInUser();
	}
}
