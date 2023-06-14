<?php

namespace Database\Repository;

use Database\Interface\Repository;

class UserStart extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_user_start", \Database\Object\UserStart::class, orderField: 'name');
	}

	public function getByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($userId, $name, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);
		$statement->where('name', $name);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
