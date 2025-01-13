<?php

namespace Database\Repository\Informat;

use ClanCats\Hydrahon\Query\Sql\Func;
use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class Registration extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_registration", \Database\Object\Informat\Registration::class, orderField: 'start', deletedField: false, guidField: 'informatGuid');
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatGuid', $informatGuid);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatStudentId($informatStudentId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatStudentId', $informatStudentId);

        return $this->executeSelect($statement);
    }

    public function getCurrentByInformatStudentId($informatStudentId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatStudentId', $informatStudentId);
        $statement->where('status', "0");
        $statement->where('start', '<=', Clock::nowAsString("Y-m-d"));
        $statement->whereNotNull('end');

        return $this->executeSelect($statement);
    }

    public function getBySchoolInstituteId($schoolInstituteId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolInstituteId', $schoolInstituteId);

        return $this->executeSelect($statement);
    }
}
