<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatStaffFreeField extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_informat_staff_freefield", \Database\Object\InformatStaffFreeField::class, orderField: "informatStaffId");
	}

	public function getByStaffId($staffId)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatStaffId', $staffId);

		return $this->executeSelect($statement);
	}

	public function getByStaffIdAndDescription($staffId, $description)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatStaffId', $staffId);
		$statement->where('description', $description);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
