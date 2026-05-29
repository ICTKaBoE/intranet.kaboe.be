<?php

namespace Database\Repository\HR;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class HR extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_hr", \Database\Object\HR\HR::class, orderField: "name");
    }

    public function getByInsz($insz)
    {
        $statement = $this->prepareSelect(filters: ["insz" => $insz]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
