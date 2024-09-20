<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class School extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school", \Database\Object\School::class, orderField: 'name', guidField: false);
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect();
        $statement->where('name', $name);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
