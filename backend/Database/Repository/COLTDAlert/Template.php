<?php

namespace Database\Repository\COLTDAlert;

use Database\Interface\Repository;

class Template extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_coltdalert_template", \Database\Object\COLTDAlert\Template::class, orderField: 'name');
    }
}
