<?php

namespace Database\Repository\Registration;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Schoolyear extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_registration_schoolyear", \Database\Object\Registration\Schoolyear::class, orderField: "name");
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect(filters: ["name" => $name]);
        return $this->executeSelect($statement);
    }
}
