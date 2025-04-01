<?php

namespace Database\Repository\Bike;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Event extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_bike_event", \Database\Object\Bike\Event::class, orderField: 'distance', guidField: false);
    }

    public function getByUserMainSchoolId($userMainSchoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userMainSchoolId', $userMainSchoolId);

        return $this->executeSelect($statement);
    }

    public function getByBikeDistanceId($bikeDistanceId)
    {
        $statement = $this->prepareSelect();
        $statement->where('bikeDistanceId', $bikeDistanceId);

        return $this->executeSelect($statement);
    }

    public function getByUserMainSchoolIdAndType($userMainSchoolId, $type)
    {
        $statement = $this->prepareSelect();
        $statement->where('userMainSchoolId', $userMainSchoolId);
        $statement->where('type', $type);

        return $this->executeSelect($statement);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);

        return $this->executeSelect($statement);
    }

    public function getByUserIdAndType($userId, $type)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);
        $statement->where('type', $type);

        return $this->executeSelect($statement);
    }

    public function getByUserIdTypeAndDate($userId, $type, $date)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);
        $statement->where('type', $type);
        $statement->where('date', $date);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
