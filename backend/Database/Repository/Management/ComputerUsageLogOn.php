<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class ComputerUsageLogOn extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_computer_usage_logon", \Database\Object\Management\ComputerUsageLogOn::class, orderField: "logon", guidField: false);
    }

    public function getByComputerId($computerId)
    {
        $statement = $this->prepareSelect();
        $statement->where('computerId', $computerId);

        return $this->executeSelect($statement);
    }

    public function getByComputerIdAndLogon($computerId, $logon)
    {
        $statement = $this->prepareSelect();
        $statement->where('computerId', $computerId);
        $statement->where('logon', $logon);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByComputerIdAndLogonBetweenStartupAndShutdown($computerId, $startup, $shutdown)
    {
        $statement = $this->prepareSelect();
        $statement->where('computerId', $computerId);

        $items = $this->executeSelect($statement);

        $items = Arrays::filter($items, fn($i) => Clock::at($i->logon)->isAfterOrEqualTo(Clock::at($startup)));
        
        if ($items) {
            if (is_null($shutdown)) $items = Arrays::filter($items, fn($i) => Clock::at($i->logon)->isAfter(Clock::at($startup)));
            else $items = Arrays::filter($items, fn ($i) =>  Clock::at($i->logon)->isBeforeOrEqualTo(Clock::at($shutdown)));
        }

        return $items;
    }
}
