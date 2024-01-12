<?php

namespace Database\Repository;

use Ouzo\Utilities\Clock;
use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class TemperatureRegistration extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_temperature_registration", \Database\Object\TemperatureRegistration::class, orderField: "creationDateTime");   
    }

    public function getBySchool($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }

    public function getBySchoolAndDate($schoolId, $start = null, $end = null)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);
        $items = $this->executeSelect($statement);

        if (!is_null($start) && !is_null($end)) {
            $items = Arrays::filter($items, fn ($i) => (Clock::at($i->creationDateTime)->isBefore(Clock::at($end)) && Clock::at($i->creationDateTime)->isAfter(Clock::at($start))));
        }

        return $items;
    }
}