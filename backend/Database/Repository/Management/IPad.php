<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class IPad extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_ipad", \Database\Object\Management\IPad::class, orderField: 'name');
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }

    public function getByJamfId($jamfId)
    {
        $statement = $this->prepareSelect();
        $statement->where('jamfId', $jamfId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
