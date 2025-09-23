<?php

namespace Database\Repository\Registration;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Option extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_registration_field_option", \Database\Object\Registration\Option::class, orderField: "name");
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

    public function getByStudyyearId($studyyearId)
    {
        $statement = $this->prepareSelect(filters: ["studyyearId" => $studyyearId]);
        return $this->executeSelect($statement);
    }

    public function getByFieldId($fieldId)
    {
        $statement = $this->prepareSelect(filters: ["fieldId" => $fieldId]);
        return $this->executeSelect($statement);
    }
}
