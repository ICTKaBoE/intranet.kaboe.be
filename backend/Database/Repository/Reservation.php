<?php

namespace Database\Repository;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;

class Reservation extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_reservation", \Database\Object\Reservation::class, orderField: 'start');
	}

	public function getByTypeSchool($userId = null, $schoolId = null)
	{
		$statement = $this->prepareSelect();
		if (!is_null($userId)) $statement->where('userId', $userId);
		if (!is_null($schoolId)) $statement->where('schoolId', $schoolId);
		return $this->executeSelect($statement);
	}

	public function getByAsset($schoolId, $type, $cartId, $assetId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		$statement->where(function ($q1) use ($type, $assetId, $cartId) {
			$q1->where('type', $type);
			$q1->where(function ($q) use ($assetId) {
				$q->where('assetId', $assetId); //check voor enkele reservaties bv 14
				$q->orWhere('assetId', 'LIKE', "%;{$assetId};%"); //check voor assetId in midden van minstens 3 bv 14;15;16
				$q->orWhere('assetId', 'LIKE', "{$assetId};%"); //check voor assetId in begin van minstens 2 bv 14,15
				$q->orWhere('assetId', 'LIKE', "%;{$assetId}"); //check voor assetId op einde van minstens 2 bv 14;15
			});

			if ($type == "L") {
				$q1->orWhere('type', "LK");
				$q1->where(function ($q) use ($cartId) {
					$q->where('assetId', $cartId); //check voor enkele reservaties bv 14
					$q->orWhere('assetId', 'LIKE', "%;{$cartId};%"); //check voor assetId in midden van minstens 3 bv 14;15;16
					$q->orWhere('assetId', 'LIKE', "{$cartId};%"); //check voor assetId in begin van minstens 2 bv 14,15
					$q->orWhere('assetId', 'LIKE', "%;{$cartId}"); //check voor assetId op einde van minstens 2 bv 14;15
				});
			} else if ($type == "I") {
				$q1->orWhere('type', "IK");
				$q1->where(function ($q) use ($cartId) {
					$q->where('assetId', $cartId); //check voor enkele reservaties bv 14
					$q->orWhere('assetId', 'LIKE', "%;{$cartId};%"); //check voor assetId in midden van minstens 3 bv 14;15;16
					$q->orWhere('assetId', 'LIKE', "{$cartId};%"); //check voor assetId in begin van minstens 2 bv 14,15
					$q->orWhere('assetId', 'LIKE', "%;{$cartId}"); //check voor assetId op einde van minstens 2 bv 14;15
				});
			}
		});

		return $this->executeSelect($statement);
	}

	public function detectOverlap($startDate, $startTime, $endDate, $endTime, $type, $assetId, $currentId = null)
	{
		$start = Clock::at("{$startDate} {$startTime}");
		$end = Clock::at("{$endDate} {$endTime}");

		$statement = $this->prepareSelect();
		if (!is_null($currentId)) $statement->where('id', "!=", $currentId);
		if (!is_null($type)) $statement->where('type', $type);

		$statement->where(function ($q) use ($assetId) {
			$q->where('assetId', $assetId); //check voor enkele reservaties bv 14
			$q->orWhere('assetId', 'LIKE', "%;{$assetId};%"); //check voor assetId in midden van minstens 3 bv 14;15;16
			$q->orWhere('assetId', 'LIKE', "{$assetId};%"); //check voor assetId in begin van minstens 2 bv 14,15
			$q->orWhere('assetId', 'LIKE', "%;{$assetId}"); //check voor assetId op einde van minstens 2 bv 14;15
		});

		$items = $this->executeSelect($statement);
		$items = Arrays::filter($items, fn ($i) => ($start->isBefore(Clock::at($i->end)) && Clock::at($i->start)->isBefore($end)));

		return $items;
	}
}
