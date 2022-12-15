<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class UserAddress extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"street",
		"number",
		"bus",
		"zipcode",
		"city",
		"country",
		"current",
		"deleted"
	];

	protected $encodeAttributes = ["street", "bus", "zipcode", "city", "country"];

	public function init()
	{
		$this->formatted = $this->street . " " . $this->number . ($this->bus ? $this->bus : "") . ", " . $this->zipcode . " "  . $this->city . ", " . $this->country;
	}
}
