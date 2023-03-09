<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Security\Input;

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

	public function init()
	{
		$this->value = Input::convertToBool($this->value);
	}
}
