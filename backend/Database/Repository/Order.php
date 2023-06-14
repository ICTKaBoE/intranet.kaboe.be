<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Order extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_order", \Database\Object\Order::class, orderField: 'id', orderDirection: 'DESC');
	}
}
