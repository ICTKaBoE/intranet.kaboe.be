<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;

class FooterComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('footer', $arguments);
	}
}
