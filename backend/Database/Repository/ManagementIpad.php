<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class ManagementIpad extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_ipad", \Database\Object\ManagementIpad::class, orderField: 'schoolId');
	}

	public function getByUDID($udid)
	{
		$statement = $this->prepareSelect();
		$statement->where('udid', $udid);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getBySchool($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getByCart($cartId)
	{
		$statement = $this->prepareSelect();
		$statement->where('cartId', $cartId);

		return $this->executeSelect($statement);
	}

	public function getBySchoolAndCart($schoolId, $cartId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('cartId', $cartId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $devicename, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("deviceName", $devicename);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
