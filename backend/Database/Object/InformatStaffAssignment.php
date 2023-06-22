<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatStaffAssignment extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatUID",
		"informatStaffUID",
		"masterNumber",
		"instituteNumber",
		"start",
		"end",
		"deleted"
	];
}
