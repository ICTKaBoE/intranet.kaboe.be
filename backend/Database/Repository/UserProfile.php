<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class UserProfile extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_user_profile", \Database\Object\UserProfile::class, orderField: 'id');
	}

	public function getByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}
}
