<?php

namespace Database\Repository;

use Database\Interface\Repository;

class HelpdeskAction extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_helpdesk_action", \Database\Object\HelpdeskAction::class, orderField: 'creationDateTime', orderDirection: 'DESC');
	}

	public function getByHelpdeskId($helpdeskId)
	{
		try {
			$statement = $this->prepareSelect();
			$statement->where("helpdeskId", $helpdeskId);

			return $this->executeSelect($statement);
		} catch (\Exception $e) {
			die(var_dump("HelpdeskAction:getByHelpdeskId - " . $e->getMessage()));
		}
	}
}
