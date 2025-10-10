<?php

namespace Database\Repository\EHBO;

use Database\Interface\Repository;

class Description extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_ehbo_description", \Database\Object\EHBO\Description::class, orderField: "name", deletedField: false, guidField: false);
    }
}
