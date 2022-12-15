<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class UserSecurity extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"userId",
		"view",
		"edit",
		"export",
		"changeSettings",
		"locked",
		"deleted",
	];
}
