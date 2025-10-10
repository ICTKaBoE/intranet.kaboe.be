<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Controllers\ApiController;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Route\Group;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class IndexController extends ApiController
{
    // Get functions
    protected function getList($view, $id = null)
    {
        $navigationRepo = new Navigation;
        $domain = str_replace(["https", "http", "://", "/"], "", Strings::equalsIgnoreCase(Helpers::request()->getHost(), Helpers::request()->getReferer()) ? Helpers::request()->getHost() : Helpers::request()->getReferer());
        $routeGroup = (new Group)->getByDomain($domain);

        $folder = Helpers::url()->getParam("folder", 0);
        $topLevelItems = $navigationRepo->getByRouteGroupIdAndParentId($routeGroup->id, $folder);
        $topLevelItems = Arrays::filter($topLevelItems, fn($tli) => $tli->order >= 0);

        $items = [];
        foreach ($topLevelItems as $tli) if (User::canAccess($tli->id)) $items[] = $tli;

        $items = Arrays::map($items, fn($i) => $i->toArray(true));
        $this->appendToJson('raw', General::processTemplate($items));
    }
}
