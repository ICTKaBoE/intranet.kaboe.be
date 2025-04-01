<?php

namespace Database\Repository\School;

use Database\Interface\Repository;

class Address extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_address", \Database\Object\School\Address::class, orderField: false, guidField: false);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }
}
