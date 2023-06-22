<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatStudentSubgroup extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_informat_student_subgroup", \Database\Object\InformatStudentSubgroup::class, orderField: "class");
	}

	public function getByInformatStudentUID($informatStudentUID)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatStudentUID', $informatStudentUID);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
