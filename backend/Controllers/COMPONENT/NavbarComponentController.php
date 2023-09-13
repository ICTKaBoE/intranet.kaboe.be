<?php

namespace Controllers\COMPONENT;

use Security\User;
use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Database\Repository\Module;
use Controllers\ComponentController;
use Database\Repository\ModuleNavigation;

class NavbarComponentController extends ComponentController
{
	private const TEMPLATE_NAVBAR = '<div class="navbar-expand-md">
										<div class="collapse navbar-collapse" id="navbar-menu">
											<div class="navbar navbar-light">
												<div class="container-fluid">
													<ul class="navbar-nav">{{navbar:items}}</ul>
												</div>
											</div>
										</div>
									</div>';

	private const TEMPLATE_NAVBAR_ITEM = '<li class="nav-item {{navbar:item:isActive}}">
											<a class="nav-link" href="{{navbar:item:link}}">
												{{navbar:item:ifIcon}}
												<span class="nav-link-title">{{navbar:item:name}}</span>
											</a>
										</li>{{navbar:items}}';

	private const TEMPLATE_NAVBAR_ITEM_ICON = 	'<span class="nav-link-icon d-md-none d-lg-inline-block">
													<i class="icon ti ti-{{navbar:item:icon}}"></i>
												</span>';

	public function __construct()
	{
		parent::__construct('navbar');
		$this->writeNavbar();
	}

	private function writeNavbar()
	{
		$module = (new Module)->getByModule(Helpers::getModule());
		$navItems = (new ModuleNavigation)->getByModuleId($module->id);

		$navItems = Arrays::filter($navItems, function ($ni) use ($module) {
			return User::hasPermissionToEnterSub($ni, $module->id, User::getLoggedInUser()->id);
		});

		if (empty($navItems)) $this->layout = str_replace("{{navbar:layout}}", "", $this->layout);
		else {
			$templateNavbar = self::TEMPLATE_NAVBAR;

			foreach ($navItems as $navItem) {
				$templateNavbarItem = self::TEMPLATE_NAVBAR_ITEM;

				if (!is_null($navItem->icon)) $templateNavbarItem = str_replace("{{navbar:item:ifIcon}}", self::TEMPLATE_NAVBAR_ITEM_ICON, $templateNavbarItem);
				foreach ($navItem->toArray() as $key => $value) $templateNavbarItem = str_replace("{{navbar:item:{$key}}}", $value, $templateNavbarItem);

				$templateNavbar = str_replace("{{navbar:items}}", $templateNavbarItem, $templateNavbar);
			}

			$this->layout = str_replace("{{navbar:layout}}", $templateNavbar, $this->layout);
		}
	}
}
