<?php

namespace Database\Repository\Remedy;

use Database\Interface\Repository;

class Moment extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_remedy_moment", \Database\Object\Remedy\Moment::class, orderField: 'date');
    }

    public function getByTypeId($typeId)
    {
        $statement = $this->prepareSelect(filters: [
            'typeId' => $typeId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect(filters: [
            'userId' => $userId
        ]);

        return $this->executeSelect($statement);
    }
}
