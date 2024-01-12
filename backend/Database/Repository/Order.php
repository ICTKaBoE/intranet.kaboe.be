<?php

namespace Database\Repository;

use Security\User;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class Order extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_order", \Database\Object\Order::class, orderField: 'id', orderDirection: 'DESC');
	}

	public function getBySchoolByStatus($schoolId, $status)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('status', $status);

		return $this->executeSelect($statement);
	}

	public function getByType($type)
	{
		try {
			$statement = $this->prepareSelect();

			if (Strings::equal($type, 'mine')) $statement->where('acceptorId', User::getLoggedInUser()->id);

			return $this->executeSelect($statement);
		} catch (\Exception $e) {
			die(var_dump("Order:getByType - " . $e->getMessage()));
		}
	}

	public function getByTypeWithFilters($type, $filters = [])
	{
		try {
			$statement = $this->prepareSelect();

			foreach ($filters as $key => $value) {
				$statement->where($key, $value);
			}

			if (Strings::equal($type, 'mine')) $statement->where('acceptorId', User::getLoggedInUser()->id);

			return $this->executeSelect($statement);
		} catch (\Exception $e) {
			die(var_dump("Order:getByType - " . $e->getMessage()));
		}
	}
}
