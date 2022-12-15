<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Setting extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"settingTabId",
		"name",
		"type",
		"options",
		"value",
		"deleted"
	];

	protected $encodeAttributes = [
		"name",
		"value"
	];
}
