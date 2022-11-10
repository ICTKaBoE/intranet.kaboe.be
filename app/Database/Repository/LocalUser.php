<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class LocalUser extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_local_user", \Database\Object\LocalUser::class, orderField: false);
	}

	public function getByUsername($username)
	{
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->username, $username));
	}

	public function getByO365Id($o365id)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->o365Id, $o365id));

		return Arrays::first($items);
	}
}
