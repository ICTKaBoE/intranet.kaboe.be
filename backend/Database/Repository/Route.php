<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Route extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_route", \Database\Object\Route::class, orderField: false);
    }

    public function getByRouteGroupId($routeGroupId)
    {
        $statement = $this->prepareSelect();
        $statement->where('routeGroupId', $routeGroupId);

        return $this->executeSelect($statement);
    }
}
