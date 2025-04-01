<?php

namespace Database\Repository\Signage;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Screen extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_signage_screen", \Database\Object\Signage\Screen::class, orderField: 'name');
    }

    public function getByCode($code)
    {
        $statement = $this->prepareSelect();
        $statement->where("code", $code);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
