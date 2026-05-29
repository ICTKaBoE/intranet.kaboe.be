<?php

namespace Database\Repository\HR;

use Database\Interface\Repository;

class History extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_hr_history", \Database\Object\HR\History::class, orderField: "datetime", orderDirection: self::ORDER_DIRECTION_DESC, deletedField: false, guidField: false);
    }

    public function getByHrId($hrId)
    {
        $statement = $this->prepareSelect(filters: ['hrId' => $hrId]);
        return $this->executeSelect($statement);
    }
}
