<?php

namespace Database\Repository\Accident;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Status extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_accident_status", \Database\Object\Accident\Status::class, deletedField: false, guidField: false);
    }

    public function getDefault()
    {
        $statement = $this->prepareSelect(filters: ["default" => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getWhenInsuranceIsMailed()
    {
        $statement = $this->prepareSelect(filters: ["whenInsuranceIsMailed" => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
