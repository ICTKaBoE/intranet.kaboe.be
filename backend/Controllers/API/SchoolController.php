<?php

namespace Controllers\API;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\School;
use Database\Repository\SchoolAddress;

class SchoolController extends ApiController
{
    public function get($view, $what = null, $id = null)
    {
        if (Strings::equal($what, null)) $this->getList($view, $id);
        else if (Strings::equal($what, "address")) $this->getAddress($view, $id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function post($what, $id = null)
    {
        // if ($what == "distance") $this->postDistance($id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    // Get Functions
    private function getList($view, $id)
    {
        $repo = new School;

        if (Strings::equal($view, "table")) {
        } else if (Strings::equal($view, "select")) {
            $items = $repo->get();
            $this->appendToJson('items', $items);
        } else if (Strings::equal($view, "form")) {
        } else if (Strings::equal($view, "list")) {
            $items = $repo->get();
            $this->appendToJson('items', $items);
        }
    }

    private function getAddress($view, $id = null)
    {
        $repo = new SchoolAddress;

        if (Strings::equal($view, "table")) {
        } else if (Strings::equal($view, "select")) {
            $address = $repo->get();
            $address = Arrays::map($address, fn($a) => $a = $a->toArray(true));
            $this->appendToJson('items', $address);
        } else if (Strings::equal($view, "form")) {
        }
    }

    // Post Functions

}
