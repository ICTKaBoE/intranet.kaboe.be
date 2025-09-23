<?php

namespace Database\Repository\TempReg;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Treshhold extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_tempreg_treshhold", \Database\Object\TempReg\Treshhold::class, orderField: "min", deletedField: false, guidField: false);
    }

    public function getByPercentageBetween($percentage)
    {
        $statement = $this->prepareSelect();
        $statement->where("min", "<=", $percentage);
        $statement->where("max", ">=", $percentage);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
