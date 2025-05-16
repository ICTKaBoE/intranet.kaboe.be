<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class ComputerUsageOnOff extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_computer_usage_onoff", \Database\Object\Management\ComputerUsageOnOff::class, orderField: "startup", guidField: false);
    }

    public function getByComputerId($computerId)
    {
        $statement = $this->prepareSelect(filters: ['computerId' => $computerId]);
        return $this->executeSelect($statement);
    }

    public function getByComputerIdAndStartup($computerId, $startup)
    {
        $statement = $this->prepareSelect(filters: [
            'computerId' => $computerId,
            'startup' => $startup
        ]);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getBetween($start, $end)
    {
        $statement = $this->prepareSelect();
        $statement->where('startup', '>=', $start);
        $statement->where('shutdown', '<=', $end);

        return $this->executeSelect($statement);
    }

    public function getByComputerIdBetween($computerId, $start, $end)
    {
        $statement = $this->prepareSelect(filters: ['computerId' => $computerId]);
        $statement->where('startup', '>=', $start);
        $statement->where('shutdown', '<=', $end);
        return $this->executeSelect($statement);
    }
}
