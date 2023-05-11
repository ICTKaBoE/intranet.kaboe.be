<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementPatchpanel extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_patchpanel", \Database\Object\ManagementPatchpanel::class, orderField: 'name');
	}

	public function getBySchool($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getByCabinet($cabinetId)
	{
		$statement = $this->prepareSelect();
		$statement->where('cabinetId', $cabinetId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $buildingId, $roomId, $cabinetId, $name, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("buildingId", $buildingId);
		$statement->where("roomId", $roomId);
		$statement->where("cabinetId", $cabinetId);
		$statement->where("name", $name);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
