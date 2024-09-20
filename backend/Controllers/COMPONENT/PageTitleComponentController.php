<?php

namespace Controllers\COMPONENT;

use Router\Helpers;
use Controllers\ComponentController;
use Database\Repository\Navigation;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class PageTitleComponentController extends ComponentController
{
	public function __construct()
	{
		parent::__construct('pagetitle');
		$this->writePageTitle();
	}

	private function writePageTitle()
	{
		$module = Helpers::getModule();
		$page = Helpers::getPage();
		$id = Helpers::getId();

		$navigationRepo = new Navigation;
		$moduleNavigation = Arrays::first($navigationRepo->getByParentIdAndLink(0, $module));
		$pageNavigation = Arrays::firstOrNull($navigationRepo->getByParentIdAndLink($moduleNavigation->id, $page));

		$pagetitle = $moduleNavigation->name . (is_null($pageNavigation) ? '' : ' - ' . $pageNavigation->name) . (is_null($id) ? '' : (Strings::equal($id, "add") ? " - Toevoegen" : " - Bewerken"));
		$this->layout = str_replace("{{page:title}}", $pagetitle, $this->layout);
	}
}
