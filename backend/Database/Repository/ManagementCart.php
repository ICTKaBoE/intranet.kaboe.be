<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementCart extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_cart", \Database\Object\ManagementCart::class, orderField: 'schoolId');
	}

	public function getBySchoolAndType($schoolId, $type)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('type', $type);
        
		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $type, $name, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("type", $type);
		$statement->where("name", $name);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
