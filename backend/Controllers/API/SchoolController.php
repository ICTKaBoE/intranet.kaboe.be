<?php

namespace Controllers\API;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\School;
use Database\Repository\SchoolAddress;
use Helpers\General;

class SchoolController extends ApiController
{
    // Get Functions
    protected function getList($view, $id)
    {
        $repo = new School;

        if (Strings::equal($view, self::VIEW_TABLE)) {
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get();
            $this->appendToJson('items', $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
        } else if (Strings::equal($view, self::VIEW_LIST)) {
            $items = $repo->get();
            $items = Arrays::map($items, fn($i) => $i->toArray(true));
            $this->appendToJson('raw', General::processTemplate($items));
        }
    }

    protected function getAddress($view, $id = null)
    {
        $repo = new SchoolAddress;

        if (Strings::equal($view, self::VIEW_TABLE)) {
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $address = $repo->get();
            $address = Arrays::map($address, fn($a) => $a = $a->toArray(true));
            $this->appendToJson('items', $address);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
        }
    }

    // Post Functions

}
