<?php

namespace Database\Repository\School;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Course extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_course", \Database\Object\School\Course::class, orderField: 'name');
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect(filters: ['name' => $name]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
