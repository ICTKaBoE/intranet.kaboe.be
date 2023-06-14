<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;

class OrderSupplier extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"name",
		"email",
		"phone",
		"contact",
		"deleted"
	];

	public function init()
	{
		$this->nameWithContact = "{$this->name} ({$this->contact})";
	}
}
