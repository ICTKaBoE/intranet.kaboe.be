<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatStudent extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatUID",
		"instituteId",
		"name",
		"firstName",
		"insz",
		"deleted"
	];

	public function init()
	{
		$this->fullName = "{$this->firstName} {$this->name}";
	}
}
