<?php

namespace Database\Repository\Order;

use Database\Interface\Repository;

class Supplier extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_order_supplier", \Database\Object\Order\Supplier::class, orderField: 'name');
    }
}
