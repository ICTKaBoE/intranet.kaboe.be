<?php

namespace Controllers\API;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\General\Country;
use Database\Repository\General\Language;
use Database\Repository\General\Nationality;

class GeneralController extends ApiController
{
    protected function getCountry($view, $id = null)
    {
        $repo = new Country;
        $items = $repo->get($id);

        if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
    }

    protected function getNationality($view, $id = null)
    {
        $repo = new Nationality;
        $items = $repo->get($id);

        if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
    }

    protected function getLanguage($view, $id = null)
    {
        $repo = new Language;
        $items = $repo->get($id);

        if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
    }
}
