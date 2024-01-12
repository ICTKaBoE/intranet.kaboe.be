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

	public function getBySchoolByStatus($schoolId, $status)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('status', $status);

		return $this->executeSelect($statement);
	}

	public function getBySchoolByPriorityByNotStatus($schoolId, $priority, $status)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('priority', $priority);
		$statement->where('status', '!=', $status);
	
		return $this->executeSelect($statement);
	}

	public function getByViewTypeWithFilters($viewType, $filters = [])
	{
		try {
			$statement = $this->prepareSelect();

			foreach ($filters as $key => $value) {
				if (!is_null($value)) $statement->where($key, $value);
			}

			if (Strings::equal($viewType, 'mine')) $statement->where('creatorId', User::getLoggedInUser()->id);
			else if (Strings::equal($viewType, 'open')) $statement->where('status', "!=", "C");
			else if (Strings::equal($viewType, 'assignedToMe')) $statement->where('assignedToId', User::getLoggedInUser()->id)->where('status', "!=", "C");
			else if (Strings::equal($viewType, 'closed')) $statement->where('status', "C");

			return $this->executeSelect($statement);
		} catch (\Exception $e) {
			die(var_dump("Helpdesk:getByViewType - " . $e->getMessage()));
		}
	}
}
