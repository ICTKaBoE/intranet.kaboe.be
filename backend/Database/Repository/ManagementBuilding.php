<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementBuilding extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_building", \Database\Object\ManagementBuilding::class, orderField: 'schoolId');
	}

	public function getBySchool($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $name, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('name', $name);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
