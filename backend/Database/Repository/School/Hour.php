<?php

namespace Database\Repository\School;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Hour extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_hour", \Database\Object\School\Hour::class, orderField: "start");
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }
}
