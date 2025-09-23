<?php

namespace Database\Repository\Accident;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Party extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_accident_party", \Database\Object\Accident\Party::class, orderField: "name", deletedField: false, guidField: false);
    }
}
