<?php

namespace Database\Repository\Navigation;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class TableDef extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_navigation_tabledef", \Database\Object\Navigation\TableDef::class, guidField: false);
    }

    public function getByNavigationId($navigationId)
    {
        $statement = $this->prepareSelect(filters: ['navigationId' => $navigationId]);
        return $this->executeSelect($statement);
    }
}
