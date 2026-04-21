<?php

namespace Database\Repository\StrategicDashboard;

use Database\Interface\Repository;

class Item extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_strategicdashboard_item", \Database\Object\StrategicDashboard\Item::class, guidField: false);
    }
}
