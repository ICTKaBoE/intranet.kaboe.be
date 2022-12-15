<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class SettingOverride extends CustomObject
{
	protected $objectAttributes = [
		"settingId",
		"moduleId",
		"value",
		"deleted"
	];

	protected $encodeAttributes = [
		"value"
	];
}
