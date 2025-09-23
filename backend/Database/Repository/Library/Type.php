<?php

namespace Database\Repository\Library;

use Database\Interface\Repository;

class Type extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_library_type", \Database\Object\Library\Type::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
