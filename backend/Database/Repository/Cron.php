<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;

class Cron extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_cron", \Database\Object\Cron::class, orderField: 'name', guidField: false, deletedField: false);
    }

    public function getByClassAndFunction($class, $function)
    {
        $statement = $this->prepareSelect(filters: ['class' => $class, 'function' => $function]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
