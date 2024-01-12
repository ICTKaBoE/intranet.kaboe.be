<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Module;

class ModuleController extends ApiController
{
	// GET
	public function getModules($view, $type = null)
	{
		$modules = (new Module)->get();

		if ($view == "select") $this->appendToJson(["items"], $modules);
		$this->handle();
	}
}
