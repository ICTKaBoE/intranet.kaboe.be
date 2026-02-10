<?php

namespace Database\Repository\StrategicDashboard;

use Database\Interface\Repository;

class StrategicDashboard extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_strategic_dashboard", \Database\Object\StrategicDashboard\StrategicDashboard::class, orderField: 'datetime', orderDirection: self::ORDER_DIRECTION_DESC, guidField: false);
    }
}
