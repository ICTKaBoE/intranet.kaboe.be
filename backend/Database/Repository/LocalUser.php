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
		try {
			$statement = $this->prepareSelect();
			$statement->where('username', $username);

			return $this->executeSelect($statement);
		} catch (\Exception $e) {
			die(var_dump("LocalUser:getByUsername - " . $e->getMessage()));
		}
	}

	public function getByO365Id($o365id)
	{
		try {
			$statement = $this->prepareSelect();
			$statement->where('o365Id', $o365id);

			return Arrays::firstOrNull($this->executeSelect($statement));
		} catch (\Exception $e) {
			die(var_dump("LocalUser:getByO365Id - " . $e->getMessage()));
		}
	}
}
