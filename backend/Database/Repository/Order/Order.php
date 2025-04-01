<?php

namespace Database\Repository\Order;

use Database\Interface\Repository;

class Order extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_order", \Database\Object\Order\Order::class, orderField: 'number', orderDirection: 'DESC');
    }

    public function getBySupplierId($supplierId)
    {
        $statement = $this->prepareSelect();
        $statement->where("supplierId", $supplierId);

        return $this->executeSelect($statement);
    }
}
