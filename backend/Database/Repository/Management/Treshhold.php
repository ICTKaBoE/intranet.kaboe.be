<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Treshhold extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_treshhold", \Database\Object\Management\Treshhold::class, orderField: "min", deletedField: false, guidField: false);
    }

    public function getByType($type)
    {
        $statement = $this->prepareSelect(filters: ['type' => $type]);
        return $this->executeSelect($statement);
    }

    public function getByTypeAndPercentageBetween($type, $percentage)
    {
        $statement = $this->prepareSelect(filters: ['type' => $type]);
        $statement->where("min", "<=", $percentage);
        $statement->where("max", ">=", $percentage);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
