<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Clock;

class Notification extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_notification", \Database\Object\Notification::class, orderField: false, guidField: false);
    }

    public function getByShowtimeNow()
    {
        $statement = $this->prepareSelect(filters: ['showtime' => Clock::nowAsString("Y-m-d H:i:0")]);
        return $this->executeSelect($statement);
    }

    public function getByShowtimeNowByUserId($userId)
    {
        $statement = $this->prepareSelect(filters: ['userId' => $userId, 'showtime' => Clock::nowAsString("Y-m-d H:i:0")]);
        return $this->executeSelect($statement);
    }
}
