<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Controllers\ApiController;
use Database\Object\Navigation as ObjectNavigation;
use Database\Repository\Navigation;
use Database\Repository\Route\Group;
use Database\Repository\Setting\Setting;
use Helpers\General;
use Ouzo\Utilities\Arrays;

class IndexController extends ApiController
{
    // Get functions
    protected function getList($view, $id = null)
    {
        $mode = (new Setting)->get("site.mode")[0]->value;
        $navigationRepo = new Navigation;
        $routeGroup = (new Group)->getByDomain(($mode == "dev" ? "dev.intranet.kaboe.be" : "intranet.kaboe.be"));

        $folder = Helpers::url()->getParams();
        $folderId = Arrays::firstOrNull($navigationRepo->getByParentIdAndLink(0, $folder))?->id ?? 0;
        $topLevelItems = $navigationRepo->getByRouteGroupIdAndParentId($routeGroup->id, $folderId);

        $items = [];

        foreach ($topLevelItems as $tli) {
            if ($tli->order < 0) continue;
            if (!User::canAccess($tli->minimumRights)) continue;

            $items[] = $tli;
        }

        $items = Arrays::map($items, fn($i) => $i->toArray(true));

        $this->appendToJson('raw', General::processTemplate($items));
    }
}
