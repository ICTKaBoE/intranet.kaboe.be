<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatStaffFreeField extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatStaffId",
		"description",
		"value",
		"deleted"
	];
}
