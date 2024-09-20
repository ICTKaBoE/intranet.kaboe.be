<?php

namespace Database\Repository;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;

class BikePrice extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_bike_price", \Database\Object\BikePrice::class, orderField: 'validFrom');
    }

    public function getBetween($date)
    {
        $statement = $this->prepareSelect();
        $statement
            ->where("validFrom", "<=", Clock::at($date)->format("Y-m-d"))
            ->where("validUntil", ">=", Clock::at($date)->format("Y-m-d"));

        return Arrays::first($this->executeSelect($statement));
    }
}
