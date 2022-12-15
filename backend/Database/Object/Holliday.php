<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Repository\School;
use Database\Interface\CustomObject;

class Holliday extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"name",
		"start",
		"end",
		"fullDay",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		return $this;
	}

	public function init()
	{
		$this->fullDay = ($this->fullDay == 1);

		if ($this->fullDay) {
			$this->start = Arrays::first(explode(" ", $this->start));
			$this->end = Arrays::first(explode(" ", $this->end));
		}
	}
}
