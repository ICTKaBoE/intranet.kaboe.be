<?php

namespace Database\Repository\EHBO;

use Database\Interface\Repository;

class EHBO extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_ehbo", \Database\Object\EHBO\EHBO::class, orderField: 'creationDateTime', orderDirection: self::ORDER_DIRECTION_DESC);
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
