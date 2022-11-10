<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\LocalUser;
use Database\Repository\UserHomeWorkDistance;

class BikeEventHomeWork extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"userAddressId",
		"userHomeWorkDistanceId",
		"date",
		"distance",
		"deleted"
	];

	public function link()
	{
		$this->user = (new LocalUser)->get($this->userId)[0];
		$this->userAddress = false;
		$this->userHomeWorkDistance = (new UserHomeWorkDistance)->get($this->userHomeWorkDistanceId)[0];
	}
}
