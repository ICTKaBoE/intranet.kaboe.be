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
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId));
	}

	public function getCurrentByUserId($userId)
	{
		$items = $this->get();
		return Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId) && Strings::equal($i->current, 1)));
	}
}
