<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Router\Helpers;
use Security\FileSystem;

class ManualComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('manual', $arguments);
		$this->writeManualText();
	}

	private function writeManualText()
	{
		$folder = Helpers::getDirectory();
		$file = "manual.html";

		$content = FileSystem::GetContent(LOCATION_FRONTEND_PAGES . "{$folder}/{$file}");
		$this->layout = str_replace("{{manual:text}}", $content ?: "Geen handleiding beschikbaar", $this->layout);
	}
}
