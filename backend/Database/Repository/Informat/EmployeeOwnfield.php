<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class EmployeeOwnfield extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_employee_ownfield", \Database\Object\Informat\EmployeeOwnfield::class, orderField: 'name', deletedField: false, guidField: 'informatGuid');
    }

    public function getByInformatEmployeeId($informatEmployeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatEmployeeId', $informatEmployeeId);

        return $this->executeSelect($statement);
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatGuid', $informatGuid);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatGuidAndEmployeeId($informatGuid, $informatEmployeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatGuid', $informatGuid);
        $statement->where('informatEmployeeId', $informatEmployeeId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatEmployeeIdAndSection($informatEmployeeId, $section)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatEmployeeId', $informatEmployeeId);
        $statement->where('section', $section);

        return $this->executeSelect($statement);
    }

    public function getByInformatEmployeeIdSectionAndName($informatEmployeeId, $section, $name)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatEmployeeId', $informatEmployeeId);
        $statement->where('section', $section);
        $statement->where('name', $name);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getBySectionAndName($section, $name)
    {
        $statement = $this->prepareSelect();
        $statement->where('section', $section);
        $statement->where('name', $name);

        return $this->executeSelect($statement);
    }
}
