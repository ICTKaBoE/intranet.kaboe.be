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
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];
}
