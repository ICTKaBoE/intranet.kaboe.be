<?php

namespace Database\Repository;

use Database\Interface\Repository;

class OrderLine extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_order_line", \Database\Object\OrderLine::class, orderField: 'id');
	}

	public function getByOrder($orderId)
	{
		$statement = $this->prepareSelect();
		$statement->where('orderId', $orderId);

		return $this->executeSelect($statement);
	}
}
