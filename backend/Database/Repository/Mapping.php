<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Mapping extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_mapping", \Database\Object\Mapping::class, idField: 'key', orderField: 'key', deletedField: false, guidField: false);
    }

    public function getWhereKeyStartsWith($string)
    {
        $statement = $this->prepareSelect();
        $statement->where('key', 'like', "$string%");

        return $this->executeSelect($statement);
    }
}
