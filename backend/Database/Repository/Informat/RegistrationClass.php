<?php

namespace Database\Repository\Informat;

use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;
use ClanCats\Hydrahon\Query\Sql\Func;
use Ouzo\Utilities\Clock;

class RegistrationClass extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_registration_class", \Database\Object\Informat\RegistrationClass::class, orderField: 'start', deletedField: false, guidField: 'informatGuid');
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatGuid', $informatGuid);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatRegistrationId($informatRegistrationId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatRegistrationId', $informatRegistrationId);

        return $this->executeSelect($statement);
    }

    public function getCurrentByInformatRegistrationId($informatRegistrationId)
    {

        $statement = $this->prepareSelect();
        $statement->where('informatRegistrationId', $informatRegistrationId);
        $statement->where('start', '<=', Clock::nowAsString("Y-m-d"));
        $statement->whereNotNull('end');

        return $this->executeSelect($statement);
    }

    public function getByInformatClassgroupId($informatClassgroupId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatClassgroupId', $informatClassgroupId);

        return $this->executeSelect($statement);
    }
}
