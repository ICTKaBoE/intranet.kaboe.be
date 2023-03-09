<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Maintenance extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"creationDate",
		"lastActionDateTime",
		"finishedByDate",
		"priority",
		"status",
		"location",
		"subject",
		"details",
		"executeBy",
		"deleted"
	];
}
