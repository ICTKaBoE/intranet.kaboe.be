<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatStaffAssignment extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_informat_staff_assignment", \Database\Object\InformatStaffAssignment::class, orderField: 'start');
	}

	public function getByInformatUID($informatUID)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatUID', $informatUID);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getByInformatStaffUID($informatStaffUID)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatStaffUID', $informatStaffUID);

		return $this->executeSelect($statement);
	}
}
