<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;

class LibraryAction extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"bookId",
		"creationDateTime",
		"description",
		"deleted"
	];

	public function init()
	{
		$this->datetime = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
	}
}
