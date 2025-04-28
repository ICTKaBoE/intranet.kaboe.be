<?php

namespace Database\Repository\Route;

use Database\Interface\Repository;

class Route extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_route", \Database\Object\Route\Route::class, orderField: false, guidField: false);
    }

    public function getByRouteGroupId($routeGroupId)
    {
        $statement = $this->prepareSelect(filters: ['routeGroupId' => $routeGroupId]);
        return $this->executeSelect($statement);
    }
}
