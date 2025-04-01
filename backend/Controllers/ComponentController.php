<?php

namespace Controllers;

use Ouzo\Utilities\Strings;
use stdClass;

class ComponentController extends stdClass
{
	protected $layout = "";

	public function __construct($componentName, $arguments = [])
	{
		$this->componentName = $componentName;
		$this->arguments = $arguments;

		$this->storeArguments();
		$this->storeLayout();
	}

	public function write()
	{
		return $this->layout;
	}

	private function storeArguments()
	{
		foreach ($_COOKIE as $key => $value) {
			if (Strings::startsWith($key, "component_{$this->componentName}_")) $_COOKIE[$key] = null;
		}

		foreach ($this->arguments as $key => $value) $_COOKIE["component_{$this->componentName}_{$key}"] = $value;
	}

	private function storeLayout()
	{
		ob_start();
		require_once LOCATION_SHARED . "/component/{$this->componentName}.php";
		$this->layout = ob_get_clean();
	}
}
