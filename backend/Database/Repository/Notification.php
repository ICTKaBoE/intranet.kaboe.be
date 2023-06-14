<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Notification extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_notification", \Database\Object\Notification::class, orderField: 'creationDateTime', orderDirection: 'DESC');
	}

	public function getByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);

		return $this->executeSelect($statement);
	}
}
