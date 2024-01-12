<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementComputer extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_computer", \Database\Object\ManagementComputer::class, orderField: 'schoolId');
	}

	public function getBySchoolAndName($schoolId, $name)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('name', $name);

		return $this->executeSelect($statement);
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

	public function getByCart($cartId)
	{
		$statement = $this->prepareSelect();
		$statement->where('cartId', $cartId);

		return $this->executeSelect($statement);
	}

	public function getBySchoolAndType($schoolId, $type)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('type', $type);

		return $this->executeSelect($statement);
	}

	public function getBySchoolAndCart($schoolId, $cartId)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("cartId", $cartId);

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
