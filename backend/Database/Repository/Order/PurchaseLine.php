<?php

namespace Database\Repository\Order;

use Database\Interface\Repository;

class PurchaseLine extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_order_purchase_line", \Database\Object\Order\PurchaseLine::class, orderField: false);
    }
}
