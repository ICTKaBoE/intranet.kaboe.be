<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class School extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"name",
		"color",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];
}
