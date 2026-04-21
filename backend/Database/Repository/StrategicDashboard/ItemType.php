<?php

namespace Database\Repository\StrategicDashboard;

use Database\Interface\Repository;

class ItemType extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_strategicdashboard_item_type", \Database\Object\StrategicDashboard\ItemType::class, guidField: false, orderField: 'name', deletedField: false);
    }
}
