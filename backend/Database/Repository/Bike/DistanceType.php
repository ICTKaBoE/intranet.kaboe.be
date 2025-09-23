<?php

namespace Database\Repository\Bike;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;

class DistanceType extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_bike_distance_type", \Database\Object\Bike\DistanceType::class, orderField: "name", deletedField: false, guidField: false);
    }
}
