<?php

namespace Controllers;

class ComponentController
{
	public $layout = "";

	public function __construct($componentName)
	{
		$this->componentName = $componentName;
		$this->storeLayout();
	}

	public function write()
	{
		return $this->getLayout();
	}

	private function getLayout()
	{
		return $this->layout;
	}

	private function storeLayout()
	{
		ob_start();
		require_once LOCATION_SHARED . "/component/{$this->componentName}.php";
		$this->layout = ob_get_clean();
	}
}
