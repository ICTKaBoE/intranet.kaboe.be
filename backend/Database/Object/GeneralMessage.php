<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

class GeneralMessage extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"pageId",
		"type",
		"content",
		"from",
		"until",
		"deleted"
	];

	public function init()
	{
		$this->show = (Clock::now()->isAfterOrEqualTo(Clock::at($this->from)) && Clock::now()->isBeforeOrEqualTo(Clock::at($this->until)));
		$this->alertType = (Strings::equalsIgnoreCase($this->type, "I") ? "success" : (Strings::equalsIgnoreCase($this->type, "W") ? "warning" : "danger"));
		$this->alertIcon = (Strings::equalsIgnoreCase($this->type, "I") ? "check" : (Strings::equalsIgnoreCase($this->type, "W") ? "alert-triangle" : "alert-circle"));
	}
}
