<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class RouteGroup extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_route_group", \Database\Object\RouteGroup::class, orderField: false, guidField: false);
    }

    public function getByDomain($domain)
    {
        $statement = $this->prepareSelect();
        $statement->where('domain', $domain);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
