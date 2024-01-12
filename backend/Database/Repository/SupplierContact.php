<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class SupplierContact extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_supplier_contact", \Database\Object\SupplierContact::class, orderField: 'name');
	}

	public function getBySupplier($supplierId)
	{
		$statement = $this->prepareSelect();
		$statement->where('supplierId', $supplierId);

		return $this->executeSelect($statement);
	}

	public function getMainContactBySupplierId($supplierId)
	{
		$statement = $this->prepareSelect();
		$statement->where("supplierId", $supplierId);
		$statement->where("isMainContact", 1);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
