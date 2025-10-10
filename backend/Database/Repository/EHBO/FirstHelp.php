<?php

namespace Database\Repository\EHBO;

use Database\Interface\Repository;

class FirstHelp extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_ehbo_firsthelp", \Database\Object\EHBO\FirstHelp::class, orderField: "name", deletedField: false, guidField: false);
    }
}
