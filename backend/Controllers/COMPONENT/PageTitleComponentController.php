<?php

namespace Controllers\COMPONENT;

use Router\Helpers;
use Controllers\ComponentController;
use Database\Repository\Navigation\Navigation;

class PageTitleComponentController extends ComponentController
{
	public function __construct($arguments = [])
	{
		parent::__construct('pagetitle', $arguments);
		$this->writePageTitle();
	}

	private function writePageTitle()
	{
		$module = Helpers::getModule();
		$page = Helpers::getPage();

		$navigationRepo = new Navigation;
		$moduleNavigation = $navigationRepo->getByLinkAndType($module, "M");
		$pageNavigation = is_null($page) ? null : $navigationRepo->getByParentIdAndLink($moduleNavigation->id, $page);

		$pagetitle = $moduleNavigation->name . (is_null($pageNavigation) ? '' : ' - ' . $pageNavigation->name);
		$this->layout = str_replace("{{page:title}}", $pagetitle, $this->layout);
	}
}
