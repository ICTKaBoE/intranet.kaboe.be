<?php

namespace Database\Repository;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;

class Holliday extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_holliday", \Database\Object\Holliday::class, orderField: 'start', guidField: false);
    }

    public function dateContainsHolliday($date)
    {
        $date = Clock::at($date);

        $items = $this->get();

        $items = Arrays::filter($items, fn($i) => ($date->isAfterOrEqualTo(Clock::at($i->start)) && $date->isBeforeOrEqualTo(Clock::at($i->end))));
        return !empty($items);
    }
}
