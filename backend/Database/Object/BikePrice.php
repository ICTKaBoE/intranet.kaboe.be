<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Router\Helpers;
use Helpers\Icon;

class BikePrice extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"validFrom",
		"validUntil",
		"amount",
		"deleted"
	];

	public function init()
	{
	}
}
