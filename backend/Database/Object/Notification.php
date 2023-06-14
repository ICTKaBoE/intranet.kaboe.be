<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Notification extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"creationDateTime",
		"message",
		"link",
		"read",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];
}
