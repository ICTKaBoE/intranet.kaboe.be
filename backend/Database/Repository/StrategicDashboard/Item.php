<?php

namespace Database\Repository\StrategicDashboard;

use Database\Interface\Repository;

class Item extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_strategicdashboard_item", \Database\Object\StrategicDashboard\Item::class, guidField: false);
    }

    public function getByCategoryId($categoryId)
    {
        $statement = $this->prepareSelect(filters: ["categoryId" => $categoryId]);
        return $this->executeSelect($statement);
    }
}
