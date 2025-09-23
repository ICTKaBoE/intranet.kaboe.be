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
}
