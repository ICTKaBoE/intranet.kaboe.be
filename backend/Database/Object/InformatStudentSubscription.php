<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatStudentSubscription extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatUID",
		"informatStudentUID",
		"instituteId",
		"status",
		"start",
		"end",
		"grade",
		"year",
		"deleted"
	];
}
