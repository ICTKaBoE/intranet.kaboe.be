<?php

namespace Database\Repository\StrategicDashboard;

use Database\Interface\Repository;

class Category extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_strategicdashboard_category", \Database\Object\StrategicDashboard\Category::class);
    }
}
