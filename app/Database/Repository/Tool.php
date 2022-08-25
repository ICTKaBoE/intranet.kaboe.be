<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Tool extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_tool", \Database\Object\Tool::class);
    }

    public function getByRoute($route) {
        $items = parent::get(order: false);
        $items = Arrays::filter($items, fn ($i) => Strings::equal($i->routeTool, $route));

        return Arrays::first($items);
    }
}
