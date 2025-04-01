<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Accident extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_accident", \Database\Object\Accident::class, orderField: 'creationDateTime', orderDirection: 'DESC');
    }
}
