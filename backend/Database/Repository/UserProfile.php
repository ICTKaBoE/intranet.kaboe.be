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
		$items = $this->get();
		return Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId)));
	}
}
