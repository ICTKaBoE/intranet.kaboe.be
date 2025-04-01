<?php

namespace Controllers\COMPONENT;

use Router\Helpers;
use Controllers\ComponentController;
use Database\Repository\Navigation;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

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
		$moduleNavigation = Arrays::firstOrNull($navigationRepo->getByParentIdAndLink(0, $module));
		$pageNavigation = Arrays::firstOrNull($navigationRepo->getByParentIdAndLink($moduleNavigation->id, $page));

		$pagetitle = $moduleNavigation->name . (is_null($pageNavigation) ? '' : ' - ' . $pageNavigation->name);
		$this->layout = str_replace("{{page:title}}", $pagetitle, $this->layout);
	}
}
