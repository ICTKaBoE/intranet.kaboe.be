<?php

namespace Database\Repository;

use Database\Interface\Repository;

class SchoolInstitute extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_institute", \Database\Object\SchoolInstitute::class, orderField: false, guidField: false);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }

    public function getByInstituteNumber($number)
    {
        $statement = $this->prepareSelect();
        $statement->where('number', $number);

        return $this->executeSelect($statement);
    }
}
