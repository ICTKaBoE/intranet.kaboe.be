<?php

namespace Controllers\API;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation;

class NavigationController extends ApiController
{
    // Get functions
    protected function getList($view, $id = null)
    {
        $repo = new Navigation;
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->getByParentId(0);
            $items = Arrays::map($items, fn($i) => $i->toArray(true));
            $items = array_merge([["id" => SELECT_ALL_ID, "name" => SELECT_ALL_VALUE]], $items);
            $this->appendToJson('items', $items);
        }
    }
}
