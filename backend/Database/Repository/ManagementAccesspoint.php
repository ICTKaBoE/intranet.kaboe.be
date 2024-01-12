<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementAccesspoint extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_accesspoint", \Database\Object\ManagementAccesspoint::class, orderField: 'schoolId');
	}

	public function getBySchool($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getByRoom($roomId)
	{
		$statement = $this->prepareSelect();
		$statement->where('roomId', $roomId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $buildingId, $roomId, $name, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("buildingId", $buildingId);
		$statement->where("roomId", $roomId);
		$statement->where("name", $name);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
