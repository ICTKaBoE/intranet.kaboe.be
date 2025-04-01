<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class ComputerBattery extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_computer_battery", \Database\Object\Management\ComputerBattery::class, orderField: false, guidField: false);
    }

    public function getByComputerId($computerId)
    {
        $statement = $this->prepareSelect();
        $statement->where('computerId', $computerId);

        return $this->executeSelect($statement);
    }

    public function getByComputerIdAndBatteryId($computerId, $batteryId)
    {
        $statement = $this->prepareSelect();
        $statement->where('computerId', $computerId);
        $statement->where('batteryId', $batteryId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
