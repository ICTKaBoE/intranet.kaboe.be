<?php

namespace Controllers\COMPONENT;

use Controllers\ComponentController;
use Database\Repository\Navigation;
use Database\Repository\Route\Group;
use Router\Helpers;

class NavigationComponentController extends ComponentController
{
    private const TEMPLATE_NAVBAR_ITEM = '<li class="nav-item {{navbar:item:formatted.isActive}}">
											<a class="nav-link" href="{{navbar:item:formatted.link}}" target="{{navbar:item:formatted.target}}">
												{{navbar:item:ifIcon}}
												<span class="nav-link-title">{{navbar:item:name}}</span>
											</a>
										</li>{{navbar:items}}';

    private const TEMPLATE_NAVBAR_ITEM_WITH_SUB =    '<li class="nav-item dropdown {{navbar:item:formatted.isActive}}">
														<a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{navbar:item:formatted.isAriaExpanded}}">
															{{navbar:item:ifIcon}}
															<span class="nav-link-title">{{navbar:item:name}}</span>
														</a>
														<div class="dropdown-menu">
															<div class="dropdown-menu-columns">
																<div class="dropdown-menu-column">
																	{{navbar:item:subitems}}
																</div>
															</div>
														</div>
													</li>{{navbar:items}}';

    private const TEMPLATE_NAVBAR_SUBITEM = '<a class="dropdown-item" href="{{navbar:subitem:formatted.link}}" target="{{navbar:subitem:formatted.target}}">{{navbar:subitem:ifIcon}}{{navbar:subitem:name}}</a>{{navbar:item:subitems}}';

    private const TEMPLATE_NAVBAR_ITEM_ICON =     '<span class="nav-link-icon">
													<i class="icon ti ti-{{navbar:item:icon}}"></i>
												</span>';

    private const TEMPLATE_NAVBAR_SUBITEM_ICON =     '<span class="nav-link-icon">
														<i class="icon ti ti-{{navbar:subitem:icon}}"></i>
													</span>';

    public function __construct($arguments = [])
    {
        parent::__construct('navigation', $arguments);
        $this->writeItems();
    }

    private function writeItems()
    {
        $navigationRepo = new Navigation;
        $domain = Helpers::url()->getHost();
        $routeGroup = (new Group)->getByDomain($domain);
        $topLevelItems = $navigationRepo->getByRouteGroupIdAndParentId($routeGroup->id, 0);

        foreach ($topLevelItems as $tli) {
            if ($tli->order < 0) continue;

            $subLevelItems = $navigationRepo->getByRouteGroupIdAndParentId($routeGroup->id, $tli->id);

            $template = (count($subLevelItems) ? self::TEMPLATE_NAVBAR_ITEM_WITH_SUB : self::TEMPLATE_NAVBAR_ITEM);
            if ($tli->icon) $template = str_replace("{{navbar:item:ifIcon}}", self::TEMPLATE_NAVBAR_ITEM_ICON, $template);
            foreach ($tli->toArray(true) as $key => $value) $template = str_replace("{{navbar:item:{$key}}}", $value ?: "", $template);

            if (count($subLevelItems)) {
                foreach ($subLevelItems as $sli) {
                    $sliTemplate = self::TEMPLATE_NAVBAR_SUBITEM;
                    if ($sli->icon) $sliTemplate = str_replace("{{navbar:subitem:ifIcon}}", self::TEMPLATE_NAVBAR_SUBITEM_ICON, $sliTemplate);
                    foreach ($sli->toArray(true) as $key => $value) $sliTemplate = str_replace("{{navbar:subitem:{$key}}}", $value ?: "", $sliTemplate);

                    $template = str_replace("{{navbar:item:subitems}}", $sliTemplate, $template);
                }
            }

            $this->layout = str_replace("{{navbar:items}}", $template, $this->layout);
        }
    }
}
