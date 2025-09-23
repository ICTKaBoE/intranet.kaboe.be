<?php

namespace Database\Repository\Registration;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Registration extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_registration", \Database\Object\Registration\Registration::class, orderField: "registrationAt", orderDirection: "DESC");
    }

    public function getBySchoolyearId($schoolyearId)
    {
        $statement = $this->prepareSelect(filters: ['schoolyearId' => $schoolyearId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }

    public function getByStudyyearId($studyyearId)
    {
        $statement = $this->prepareSelect(filters: ['studyyearId' => $studyyearId]);
        return $this->executeSelect($statement);
    }

    public function getByFieldId($fieldId)
    {
        $statement = $this->prepareSelect(filters: ['fieldId' => $fieldId]);
        return $this->executeSelect($statement);
    }

    public function getByOptionId($optionId)
    {
        $statement = $this->prepareSelect(filters: ['optionId' => $optionId]);
        return $this->executeSelect($statement);
    }

    public function getByTalentId($talentId)
    {
        $statement = $this->prepareSelect(filters: ['talentId' => $talentId]);
        return $this->executeSelect($statement);
    }

    public function getByCLILId($clilId)
    {
        $statement = $this->prepareSelect(filters: ['clilId' => $clilId]);
        return $this->executeSelect($statement);
    }
}
