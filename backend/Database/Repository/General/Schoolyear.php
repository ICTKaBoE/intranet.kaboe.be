<?php

namespace Database\Repository\General;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class Schoolyear extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_general_schoolyear", \Database\Object\General\Schoolyear::class, orderField: 'name', deletedField: false);
    }

    public function getByName($name)
    {
        $statement = $this->prepareSelect(filters: ['name' => $name]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getCurrent()
    {
        $statement = $this->prepareSelect(filters: ['current' => true]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByDate($date)
    {
        $statement = $this->prepareSelect();
        $items = $this->executeSelect($statement);
        $items = Arrays::filter($items, fn($i) => Clock::at($i->start)->isBeforeOrEqualTo(Clock::at($date)) && Clock::at($i->end)->isAfterOrEqualTo(Clock::at($date)));
        return Arrays::firstOrNull($items);
    }
}
