<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

class SupervisionEvent extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_supervision_event", \Database\Object\SupervisionEvent::class, orderField: 'start');
	}

	public function getByUserId($userId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);

		return $this->executeSelect($statement);
	}

	public function getBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('userMainSchoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function detectOverlap($start, $end, $userId, $currentId = null)
	{
		$start = Clock::at($start);
		$end = Clock::at($end);

		$statement = $this->prepareSelect();
		$statement->where('userId', $userId);
		if (!is_null($currentId)) $statement->where('id', "!=", $currentId);

		$items = $this->executeSelect($statement);
		$items = Arrays::filter($items, fn ($i) => ($start->isBefore(Clock::at($i->end)) && Clock::at($i->start)->isBefore($end)));

		return $items;
	}
}
