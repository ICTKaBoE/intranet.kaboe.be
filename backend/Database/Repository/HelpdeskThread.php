<?php

namespace Database\Repository;

use Database\Interface\Repository;

class HelpdeskThread extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_helpdesk_thread", \Database\Object\HelpdeskThread::class, orderField: 'creationDateTime');
	}

	public function getByHelpdeskId($helpdeskId)
	{
		try {
			$statement = $this->prepareSelect();
			$statement->where("helpdeskId", $helpdeskId);

			return $this->executeSelect($statement);
		} catch (\Exception $e) {
			die(var_dump("HelpdeskThread:getByHelpdeskId - " . $e->getMessage()));
		}
	}
}
