<?php

namespace Database\Repository\General;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Language extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_general_language", \Database\Object\General\Language::class, orderField: 'name', deletedField: false);
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect(filters: ['name' => $name]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByCode($code)
    {
        $statement = $this->prepareSelect(filters: ['code' => $code]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
