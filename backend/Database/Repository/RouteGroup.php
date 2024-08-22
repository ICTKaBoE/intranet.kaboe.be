<?php

namespace Database\Repository;

use Database\Interface\Repository;

class RouteGroup extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_route_group", \Database\Object\RouteGroup::class, orderField: false);
    }
}
