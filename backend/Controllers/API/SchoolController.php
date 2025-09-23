<?php

namespace Controllers\API;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\School\School;
use Database\Repository\School\Address;
use Helpers\General;

class SchoolController extends ApiController
{
    // Get Functions
    protected function getList($view, $id)
    {
        $repo = new School;

        if (Strings::equal($view, self::VIEW_TABLE)) {
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $_items = $repo->get();
            $_optgroups = $items = [];
            $virtual = Arrays::filter($_items, fn($i) => $i->virtual);
            $_items = Arrays::filter($_items, fn($i) => !$i->virtual);
            foreach ($virtual as $v) $_optgroups[] = ["id" => $v->id, "name" => $v->name];
            foreach ($_items as $i) $items[] = ["optgroup" => $i->parentSchoolId, ...$i->toArray()];

            $this->appendToJson('optgroups', $_optgroups);
            $this->appendToJson('items', $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
        } else if (Strings::equal($view, self::VIEW_LIST)) {
            $items = $repo->get();
            $items = Arrays::map($items, fn($i) => $i->toArray(true));
            $this->appendToJson('raw', General::processTemplate($items));
        }
    }

    protected function getAll($view, $id = null)
    {
        $repo = new School;
        if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', Arrays::map($repo->get(), fn($i) => $i = $i->toArray(true)));
    }

    protected function getAddress($view, $id = null)
    {
        $repo = new Address;

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
