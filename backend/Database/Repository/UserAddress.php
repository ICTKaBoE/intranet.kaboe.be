<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class UserAddress extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_user_address", \Database\Object\UserAddress::class, orderField: 'id');
	}

	public function getByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);

		return $this->executeSelect($statement);
	}

	public function getCurrentByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);
		$statement->where('current', 1);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
