<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class BikeProfile extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_bike_profile", \Database\Object\BikeProfile::class);
    }

    public function getByUpn($upn)
    {
        $items = $this->get(order: false);
        $items = Arrays::filter($items, fn ($i) => Strings::equal($upn, $i->upn));

        return Arrays::firstOrNull($items);
    }
}
