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
        $statement = $this->prepareSelect(filters: ['entraId' => $entraId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect(filters: ['name' => $name]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolIdAndLikeName($schoolId, $likeName)
    {
        if (!$schoolId || !$likeName) return [];

        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        $statement->where("name", "LIKE", $likeName);
        return $this->executeSelect($statement);
    }
}
