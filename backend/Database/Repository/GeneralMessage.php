<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class GeneralMessage extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_general_message", \Database\Object\GeneralMessage::class, orderField: 'from', orderDirection: 'DESC');
	}

	public function getByModuleId($moduleId)
	{
		$statement = $this->prepareSelect();
		$statement->where('moduleId', 0);
		$statement->orWhere('moduleId', $moduleId);

		return $this->executeSelect($statement);
	}
}
