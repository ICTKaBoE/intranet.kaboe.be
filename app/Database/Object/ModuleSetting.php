<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class ModuleSetting extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"key",
		"value",
		"deleted"
	];
}
