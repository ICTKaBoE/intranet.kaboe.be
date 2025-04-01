<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;

class ActionButtonsComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('actionButtons', $arguments);
	}
}
