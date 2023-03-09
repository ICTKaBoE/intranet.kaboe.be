<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class NoteScreenPage extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"name",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];
}
