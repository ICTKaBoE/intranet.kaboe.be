<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\Mapping;

class UserStart extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"type",
		"url",
		"name",
		"icon",
		"width",
		"deleted"
	];

	public function init()
	{
		$this->typeFull = Mapping::get("user/start/{$this->type}");
	}
}
