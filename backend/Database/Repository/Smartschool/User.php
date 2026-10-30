<?php

namespace Database\Repository\Smartschool;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class User extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_smartschool_user", \Database\Object\Smartschool\User::class, orderField: false, guidField: false, deletedField: false);
    }

    public function getByInternalId($internalId)
    {
        $statement = $this->prepareSelect(filters: ["internalId" => $internalId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
