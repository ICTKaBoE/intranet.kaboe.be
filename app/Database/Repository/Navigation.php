<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class Navigation extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_navigation", \Database\Object\Navigation::class);
    }

    public function getByToolId($toolId)
    {
        $items = $this->get();
        $items = Arrays::filter($items, fn ($i) => $i->toolId === $toolId && $i->show === 1);

        return array_values($items);
    }

    public function getByToolAndRoute($toolId, $route)
    {
        $items = parent::get();
        $items = Arrays::filter($items, fn ($i) => Strings::equal($i->routePage, $route) && Strings::equal($i->toolId, $toolId));

        return Arrays::firstOrNull($items);
    }
}
