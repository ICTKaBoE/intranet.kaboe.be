<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class ClassGroup extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_classgroup", \Database\Object\Informat\ClassGroup::class, orderField: 'name', deletedField: false, guidField: 'informatGuid');
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect(filters: [
            'informatId' => $informatId
        ]);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect(filters: ['informatGuid' => $informatGuid]);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getBySchoolInstituteId($schoolInstituteId)
    {
        $statement = $this->prepareSelect(filters: ['schoolInstituteId' => $schoolInstituteId]);

        return $this->executeSelect($statement);
    }

    public function getBySchoolInstituteIdSchoolyearAndType($schoolInstituteId, $schoolyear, $type)
    {
        $statement = $this->prepareSelect(filters: [
            'schoolInstituteId' => $schoolInstituteId,
            'schoolyear' => $schoolyear,
            'type' => $type
        ]);

        return $this->executeSelect($statement);
    }

    public function getBySchoolyear($schoolyear)
    {
        $statement = $this->prepareSelect(filters: [
            'schoolyear' => $schoolyear
        ]);

        return $this->executeSelect($statement);
    }

    public function getBySchoolInstituteIdAdministrativeGroupCodeSchoolyearAndType($schoolInstituteId, $administrativeGroupCode, $schoolyear, $type)
    {
        $statement = $this->prepareSelect(filters: [
            'schoolInstituteId' => $schoolInstituteId,
            'administrativeGroupCode' => $administrativeGroupCode,
            'schoolyear' => $schoolyear,
            'type' => $type
        ]);

        return $this->executeSelect($statement);
    }
}
