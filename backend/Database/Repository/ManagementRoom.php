<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementRoom extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_room", \Database\Object\ManagementRoom::class, orderField: 'schoolId');
	}

	public function getBySchool($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getByBuilding($buildingId)
	{
		$statement = $this->prepareSelect();
		$statement->where("buildingId", $buildingId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $buildingId, $floor, $number, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("buildingId", $buildingId);
		$statement->where("floor", $floor);
		$statement->where("number", $number);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
