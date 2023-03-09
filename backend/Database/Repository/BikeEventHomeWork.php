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
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId));
	}

	public function getByIdAndDate($userId, $date)
	{
		$items = $this->get();
		return Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->userId, $userId) && Strings::equal($i->date, $date)));
	}

	public function getBySchoolId($schoolId)
	{
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->userMainSchoolId, $schoolId));
	}
}
