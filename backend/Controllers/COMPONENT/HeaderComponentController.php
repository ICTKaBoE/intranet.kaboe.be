<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Router\Helpers;
use Security\User;

class HeaderComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('header', $arguments);
	}
}
