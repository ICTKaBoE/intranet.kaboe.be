<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Source extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_source", \Database\Object\Source::class, orderField: false, guidField: false);
    }
}
