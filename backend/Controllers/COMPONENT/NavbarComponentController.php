<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Route\Group;
use Ouzo\Utilities\Arrays;
use Router\Helpers;
use Security\User;

class NavbarComponentController extends ComponentController
{
	private const TEMPLATE_NAVBAR_ITEM = '<li class="nav-item {{navbar:item:formatted.isActive}}">
											<a class="nav-link" href="{{navbar:item:formatted.link}}" target="{{navbar:item:formatted.target}}">
												{{navbar:item:ifIcon}}
												<span class="nav-link-title">{{navbar:item:name}}</span>
											</a>
										</li>{{navbar:items}}';

	private const TEMPLATE_NAVBAR_ITEM_ICON = 	'<span class="nav-link-icon">
													<i class="icon ti ti-{{navbar:item:icon}}"></i>
												</span>';

	private const TEMPLATE_MAGEMENT = '<div class="hr-text">Beheer</div>{{navbar:items}}';

	public function __construct($arguments = [])
	{
		parent::__construct('navbar', $arguments);
		$this->writeItems();
	}

	private function writeItems()
	{
		$navigationRepo = new Navigation;
		$domain = Helpers::url()->getHost();
		$moduleItem = $navigationRepo->getByLinkAndType(Helpers::getModule(), "M");

		if ($moduleItem->order >= 0 && User::canAccess($moduleItem->id) && $moduleItem->formatted->active) {
			$topLevelItems = $navigationRepo->getByParentId($moduleItem->id);
			$nonMgmt = Arrays::filter($topLevelItems, fn($i) => !$i->management);
			$mgmt = Arrays::filter($topLevelItems, fn($i) => $i->management);

			foreach ($nonMgmt as $tli) {
				if ($tli->order < 0) continue;
				if (!User::canAccess($tli->id)) continue;

				$template = self::TEMPLATE_NAVBAR_ITEM;
				if ($tli->icon) $template = str_replace("{{navbar:item:ifIcon}}", self::TEMPLATE_NAVBAR_ITEM_ICON, $template);
				foreach ($tli->toArray(true) as $key => $value) $template = str_replace("{{navbar:item:{$key}}}", $value ?: "", $template);

				$this->layout = str_replace("{{navbar:items}}", $template, $this->layout);
			}

			if (count($mgmt)) {
				$this->layout = str_replace("{{navbar:items}}", self::TEMPLATE_MAGEMENT, $this->layout);

				foreach ($mgmt as $tli) {
					if ($tli->order < 0) continue;
					if (!User::canAccess($tli->id)) continue;

					$template = self::TEMPLATE_NAVBAR_ITEM;
					if ($tli->icon) $template = str_replace("{{navbar:item:ifIcon}}", self::TEMPLATE_NAVBAR_ITEM_ICON, $template);
					foreach ($tli->toArray(true) as $key => $value) $template = str_replace("{{navbar:item:{$key}}}", $value ?: "", $template);

					$this->layout = str_replace("{{navbar:items}}", $template, $this->layout);
				}
			}
		}
	}
}
