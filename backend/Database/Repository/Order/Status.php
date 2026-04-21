<?php

namespace Database\Repository\Order;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Status extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_order_status", \Database\Object\Order\Status::class, deletedField: false, guidField: false);
    }

    public function getMailQuote()
    {
        $statement = $this->prepareSelect(filters: ['mailQuote' => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getMailAccept()
    {
        $statement = $this->prepareSelect(filters: ['mailAccept' => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getMailOrder()
    {
        $statement = $this->prepareSelect(filters: ['mailOrder' => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
