<?php

namespace Controllers\API;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Route\Group;

class NavigationController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "navigation";

    // Get functions
    protected function getList($view, $id = null)
    {
        $repo = new Navigation;
        $domain = str_replace(["https", "http", "://", "/"], "", Strings::equalsIgnoreCase(Helpers::request()->getHost(), Helpers::request()->getReferer()) ? Helpers::request()->getHost() : Helpers::request()->getReferer());

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->getByRouteGroupId((new Group)->getByDomain($domain)->id);
            $items = Arrays::filter($items, fn($i) => Strings::equal($i->type, "P"));
            $items = array_merge([["id" => SELECT_ALL_ID, "name" => SELECT_ALL_VALUE]], $items);
            $this->appendToJson('items', $items);
        }
    }

    protected function getExtended($view, $id = null)
    {
        $repo = new Navigation;
        $domain = str_replace(["https", "http", "://", "/"], "", Strings::equalsIgnoreCase(Helpers::request()->getHost(), Helpers::request()->getReferer()) ? Helpers::request()->getHost() : Helpers::request()->getReferer());

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $optgroups = $repo->getByRouteGroupId((new Group)->getByDomain($domain)->id);
            $optgroups = Arrays::filter($optgroups, fn($o) => Strings::equal($o->type, "M"));
            $optgroups = Arrays::map($optgroups, fn($o) => ["id" => $o->id, "name" => $o->name]);

            $items = [];
            foreach ($optgroups as $optgroup) $items = array_merge($items, Arrays::map($repo->getByParentId($optgroup['id']), fn($i) => ["optgroupName" => $optgroup["name"], ...$i->toArray()]));

            $this->appendToJson('optgroups', $optgroups);
            $this->appendToJson('items', $items);
        }
    }

    protected function getLinks($view, $id = null)
    {
        $repo = new Navigation;
        $domain = str_replace(["https", "http", "://", "/"], "", Strings::equalsIgnoreCase(Helpers::request()->getHost(), Helpers::request()->getReferer()) ? Helpers::request()->getHost() : Helpers::request()->getReferer());

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->getByRouteGroupId((new Group)->getByDomain($domain)->id);
            $items = Arrays::filter($items, fn($i) => Strings::equal($i->type, "L"));

            $this->appendToJson('items', array_values($items));
        }
    }
}
