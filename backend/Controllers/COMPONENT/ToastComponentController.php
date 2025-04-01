<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;

class ToastComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('toast', $arguments);
	}
}
