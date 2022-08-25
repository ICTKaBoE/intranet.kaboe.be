<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class BikeEvent extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_bike_event", \Database\Object\BikeEvent::class, orderField: 'date');
    }

    public function getByUpn($upn)
    {
        $items = $this->get();
        $items = Arrays::filter($items, fn ($i) => Strings::equal($upn, $i->upn));

        return array_values($items);
    }

    public function getByDateAndUpn($date, $upn)
    {
        $items = $this->get();
        $items = Arrays::filter($items, fn ($i) => Strings::equal($date, $i->date));
        $items = Arrays::filter($items, fn ($i) => Strings::equal($upn, $i->upn));

        return Arrays::firstOrNull($items);
    }
}
