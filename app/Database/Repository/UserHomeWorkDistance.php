<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class UserHomeWorkDistance extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_user_home_work_distance", \Database\Object\UserHomeWorkDistance::class, orderField: false);
	}

	public function getByUserId($userId)
	{
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId));
	}
}
