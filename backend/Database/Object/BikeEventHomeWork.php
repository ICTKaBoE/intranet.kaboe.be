<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\LocalUser;
use Database\Repository\UserAddress;
use Database\Repository\UserHomeWorkDistance;

class BikeEventHomeWork extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"userAddressId",
		"endSchoolId",
		"userMainSchoolId",
		"userHomeWorkDistanceId",
		"date",
		"distance",
		"pricePerKm",
		"deleted"
	];

	public function link()
	{
		$this->user = (new LocalUser)->get($this->userId)[0];
		$this->userAddress = (new UserAddress)->get($this->userAddressId)[0];
		$this->userHomeWorkDistance = (new UserHomeWorkDistance)->get($this->userHomeWorkDistanceId)[0];
		return $this;
	}
}
