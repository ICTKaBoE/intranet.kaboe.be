<?php

namespace Database\Repository\Order;

use Database\Interface\Repository;

class Line extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_order_line", \Database\Object\Order\Line::class, orderField: false, guidField: false);
    }

    public function getByOrderId($orderId)
    {
        $statement = $this->prepareSelect();
        $statement->where("orderId", $orderId);

        return $this->executeSelect($statement);
    }
}
