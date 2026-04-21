<?php

namespace Database\Repository\School;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Direction extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_direction", \Database\Object\School\Direction::class, orderField: "name");
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }
}
