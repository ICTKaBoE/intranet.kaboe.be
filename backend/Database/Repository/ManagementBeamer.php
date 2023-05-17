<?php

namespace Database\Repository;

use Database\Interface\Repository;

class ManagementBeamer extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_management_beamer", \Database\Object\ManagementBeamer::class, orderField: 'schoolId');
	}

	public function getBySchool($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $serialnumber, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('serialnumber', $serialnumber);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
