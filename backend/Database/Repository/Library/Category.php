<?php

namespace Database\Repository\Library;

use Database\Interface\Repository;

class Category extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_library_category", \Database\Object\Library\Category::class, orderField: 'name');
    }
}
