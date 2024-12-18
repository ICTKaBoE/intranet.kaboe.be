<?php

namespace Controllers\API;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Country;

class CountryController extends ApiController
{
    protected function getList($view, $id = null)
    {
        $repo = new Country;
        $items = $repo->get($id);

        if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
    }
}
