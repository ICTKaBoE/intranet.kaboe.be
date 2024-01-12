<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementFirewall extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_firewall", \Database\Object\ManagementFirewall::class, orderField: 'schoolId');
	}

	public function getBySchool($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $buildingId, $roomId, $cabinetId, $hostname, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("buildingId", $buildingId);
		$statement->where("roomId", $roomId);
		$statement->where("cabinetId", $cabinetId);
		$statement->where("hostname", $hostname);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
