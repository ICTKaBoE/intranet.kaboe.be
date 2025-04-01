<?php

namespace Database\Repository\Library;

use Database\Interface\Repository;

class Author extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_library_author", \Database\Object\Library\Author::class, orderField: 'name');
    }
}
