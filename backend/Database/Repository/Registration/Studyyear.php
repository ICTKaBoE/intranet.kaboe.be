<?php

namespace Database\Repository\Registration;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Studyyear extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_registration_studyyear", \Database\Object\Registration\Studyyear::class, orderField: "name");
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ["schoolId" => $schoolId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolyearId($schoolyearId)
    {
        $statement = $this->prepareSelect(filters: ["schoolyearId" => $schoolyearId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolIdSchoolyearIdAndName($schoolId, $schoolyearId, $name)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId, 'schoolyearId' => $schoolyearId, 'name' => $name]);
        return $this->executeSelect($statement);
    }
}
