<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;

class ModalComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('modal', $arguments);
	}
}
