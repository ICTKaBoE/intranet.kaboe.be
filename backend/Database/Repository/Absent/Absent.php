<?php

namespace Database\Repository\Absent;

use Database\Interface\Repository;

class Absent extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_absent", \Database\Object\Absent\Absent::class, orderField: 'creationDateTime', orderDirection: 'DESC');
    }

    public function getByCreatorUserId($creatorUserId)
    {
        $statement = $this->prepareSelect(filters: ['creatorUserId' => $creatorUserId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }
}
