<?php

namespace Database\Repository\Export;

use Database\Interface\Repository;

class Export extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_export", \Database\Object\Export\Export::class, orderField: "start", orderDirection: self::ORDER_DIRECTION_DESC, deletedField: false);
    }

    public function getByStatus($status)
    {
        $statement = $this->prepareSelect(filters: ['status' => $status]);
        return $this->executeSelect($statement);
    }
}
