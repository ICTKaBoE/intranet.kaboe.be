<?php

namespace Database\Repository;

use Database\Interface\Repository;

class SchoolInstitute extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_institute", \Database\Object\SchoolInstitute::class, orderField: false);
    }
}
