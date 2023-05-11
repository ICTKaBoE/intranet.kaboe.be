<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;

class HelpdeskAction extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"helpdeskId",
		"creationDateTime",
		"description",
		"deleted"
	];

	public function init()
	{
		$this->datetime = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
	}
}
