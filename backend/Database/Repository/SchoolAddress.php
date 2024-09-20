<?php

namespace Database\Repository;

use Database\Interface\Repository;

class SchoolAddress extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_address", \Database\Object\SchoolAddress::class, orderField: false, guidField: false);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }
}
