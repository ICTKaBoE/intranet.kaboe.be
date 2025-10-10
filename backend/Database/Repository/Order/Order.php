<?php

namespace Database\Repository\Order;

use Database\Interface\Repository;

class Order extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_order", \Database\Object\Order\Order::class, orderField: 'id', orderDirection: self::ORDER_DIRECTION_DESC);
    }

    public function getBySupplierId($supplierId)
    {
        $statement = $this->prepareSelect(filters: ['supplierId' => $supplierId]);
        return $this->executeSelect($statement);
    }
}
