<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatStudentSubgroup extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatStudentUID",
		"class",
		"deleted"
	];
}
