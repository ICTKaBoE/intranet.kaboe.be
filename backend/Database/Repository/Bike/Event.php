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
        $statement = $this->prepareSelect(filters: [
            'userMainSchoolId' => $userMainSchoolId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByBikeDistanceId($bikeDistanceId)
    {
        $statement = $this->prepareSelect(filters: [
            'bikeDistanceId' => $bikeDistanceId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByUserMainSchoolIdAndType($userMainSchoolId, $type)
    {
        $statement = $this->prepareSelect(filters: [
            'userMainSchoolId' => $userMainSchoolId,
            'type' => $type
        ]);

        return $this->executeSelect($statement);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect(filters: [
            'userId' => $userId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByUserIdAndType($userId, $type)
    {
        $statement = $this->prepareSelect(filters: [
            'userId' => $userId,
            'type' => $type
        ]);

        return $this->executeSelect($statement);
    }

    public function getByUserIdAndTypeDistanceMoreThenZero($userId, $type)
    {
        $statement = $this->prepareSelect(filters: [
            'userId' => $userId,
            'type' => $type
        ])
            ->where('distance', '>', 0);

        return $this->executeSelect($statement);
    }

    public function getByUserIdAndTypeDistanceMoreThenZeroBetweenDates($userId, $type, $start, $end)
    {
        $statement = $this->prepareSelect(filters: [
            'userId' => $userId,
            'type' => $type
        ])
            ->where('distance', '>', 0)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end);

        return $this->executeSelect($statement);
    }

    public function getByUserIdTypeAndDate($userId, $type, $date)
    {
        $statement = $this->prepareSelect(filters: [
            'userId' => $userId,
            'type' => $type,
            'date' => $date
        ]);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
