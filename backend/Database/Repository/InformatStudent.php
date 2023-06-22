<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatStudent extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_informat_student", \Database\Object\InformatStudent::class, orderField: 'name');
	}

	public function getByInformatUID($informatUID)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatUID', $informatUID);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
