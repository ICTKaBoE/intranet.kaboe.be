<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Supplier extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_supplier", \Database\Object\Supplier::class, orderField: 'name');
	}
}
