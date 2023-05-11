<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Strings;
use Security\User;

class Helpdesk extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_helpdesk", \Database\Object\Helpdesk::class, orderField: 'lastActionDateTime', orderDirection: 'DESC');
	}

	public function getByViewType($viewType)
	{
		try {
			$statement = $this->prepareSelect();

			if (Strings::equal($viewType, 'mine')) $statement->where('creatorId', User::getLoggedInUser()->id);
			else if (Strings::equal($viewType, 'open')) $statement->where('status', "!=", "C");
			else if (Strings::equal($viewType, 'assignedToMe')) $statement->where('assignedToId', User::getLoggedInUser()->id);
			else if (Strings::equal($viewType, 'closed')) $statement->where('status', "C");

			return $this->executeSelect($statement);
		} catch (\Exception $e) {
			die(var_dump("Helpdesk:getByViewType - " . $e->getMessage()));
		}
	}
}
