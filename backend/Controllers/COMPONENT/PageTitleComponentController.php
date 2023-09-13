<?php

namespace Controllers\COMPONENT;

use Router\Helpers;
use Database\Repository\Module;
use Controllers\ComponentController;
use Database\Repository\ModuleNavigation;

class PageTitleComponentController extends ComponentController
{
	public function __construct()
	{
		parent::__construct('pagetitle');
		$this->writePageTitle();
	}

	private function writePageTitle()
	{
		$module = (new Module)->getByModule(Helpers::getModule());
		$page = (new ModuleNavigation)->getByModuleAndPage($module->id, Helpers::getPage());

		$this->layout = str_replace("{{page:title}}", $module->name . (is_null($page) ? '' : " - " . $page->name), $this->layout);
	}
}
