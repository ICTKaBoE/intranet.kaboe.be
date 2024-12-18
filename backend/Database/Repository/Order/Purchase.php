<?php

namespace Database\Repository\Order;

use Database\Interface\Repository;

class Purchase extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_order_purchase", \Database\Object\Order\Purchase::class, orderField: 'number', orderDirection: 'DESC');
    }

    public function getBySupplierId($supplierId)
    {
        $statement = $this->prepareSelect();
        $statement->where("supplierId", $supplierId);

        return $this->executeSelect($statement);
    }
}
