<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Repository\LocalUser;
use Database\Interface\CustomObject;

class HelpdeskThread extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"helpdeskId",
		"creationDateTime",
		"creatorId",
		"content",
		"deleted"
	];

	public function init()
	{
		$this->datetime = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
	}

	public function link()
	{
		$localUserRepo = new LocalUser;
		$this->creator = $localUserRepo->get($this->creatorId)[0];

		return $this;
	}
}
