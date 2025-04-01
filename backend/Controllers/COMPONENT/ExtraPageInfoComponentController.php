<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;

class ExtraPageInfoComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('extraPageInfo', $arguments);
	}
}
