<?php

namespace Database\Repository\Remedy;

use Database\Interface\Repository;

class ComputerType extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_remedy_computer_type", \Database\Object\Remedy\ComputerType::class, orderField: "name", guidField: false);
    }
}
