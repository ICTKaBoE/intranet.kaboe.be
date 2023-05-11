<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class School extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"name",
		"color",
		"logo",
		"deviceNamePrefix",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];
}
