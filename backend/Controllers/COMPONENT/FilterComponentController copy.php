<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Router\Helpers;
use Security\FileSystem;

class FilterComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('manual', $arguments);
		$this->writeFilterContent();
	}

	private function writeFilterContent()
	{
		$folder = Helpers::getDirectory();
		$file = "filter.html";

		$content = FileSystem::GetContent(LOCATION_FRONTEND_PAGES . "{$folder}/{$file}");
		$this->layout = str_replace("{{filter:fields}}", $content ?: "", $this->layout);
	}
}
