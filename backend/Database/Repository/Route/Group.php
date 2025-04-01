<?php

namespace Database\Repository\Route;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Group extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_route_group", \Database\Object\Route\Group::class, orderField: false, guidField: false);
    }

    public function getByDomain($domain)
    {
        $statement = $this->prepareSelect();
        $statement->where('domain', $domain);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
