<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Computer extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_computer", \Database\Object\Management\Computer::class, orderField: 'name');
    }

    public function getByEntraId($entraId)
    {
        $statement = $this->prepareSelect();
        $statement->where('entraId', $entraId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect();
        $statement->where('name', $name);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }
}
