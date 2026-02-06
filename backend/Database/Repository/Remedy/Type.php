<?php

namespace Database\Repository\Remedy;

use Database\Interface\Repository;

class Type extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_remedy_type", \Database\Object\Remedy\Type::class, orderField: "name");
    }
}
