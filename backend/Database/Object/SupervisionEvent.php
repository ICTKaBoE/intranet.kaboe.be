<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Repository\LocalUser;
use Database\Interface\CustomObject;

class SupervisionEvent extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"userMainSchoolId",
		"start",
		"end",
		"deleted"
	];

	public function init()
	{
		$start = Clock::at($this->start)->toDateTime();
		$end = Clock::at($this->end)->toDateTime();
		$diff = $start->diff($end);

		$this->diffInMinutes = (($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i);
	}

	public function link()
	{
		$this->user = (new LocalUser)->get($this->userId)[0];
		return $this;
	}
}
