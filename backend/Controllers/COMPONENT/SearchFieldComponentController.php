<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;

class SearchFieldComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('searchField', $arguments);
	}
}
