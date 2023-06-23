<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\Input;

class ModuleSetting extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"key",
		"value",
		"deleted"
	];

	protected $encodeAttributes = [
		"value"
	];

	public function init()
	{
		if (!Arrays::contains([11], $this->moduleId)) {
			$this->value = Input::convertToBool($this->value);
		}
	}
}
