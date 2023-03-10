<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class BikeEventHomeWork extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_bike_event_home_work", \Database\Object\BikeEventHomeWork::class, orderField: 'date', orderDirection: 'DESC');
	}

	public function getByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);

		return $this->executeSelect($statement);
	}

	public function getByIdAndDate($userId, $date)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);
		$statement->where('date', $date);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userMainSchoolId', $schoolId);

		return $this->executeSelect($statement);
	}
}
