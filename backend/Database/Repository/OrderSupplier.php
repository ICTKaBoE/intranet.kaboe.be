<?php

namespace Database\Repository;

use Database\Interface\Repository;

class OrderSupplier extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_order_supplier", \Database\Object\OrderSupplier::class, orderField: 'name');
	}

	public function checkAlreadyExist($name, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where('name', $name);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
