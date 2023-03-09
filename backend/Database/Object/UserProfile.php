<?php

namespace Database\Object;

use Database\Repository\School;
use Database\Interface\CustomObject;
use Database\Repository\LocalUser;

class UserProfile extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"mainSchoolId",
		"bankAccount",
		"deleted"
	];

	public function init()
	{
	}

	public function link()
	{
		$this->mainSchool = (new School)->get($this->mainSchoolId)[0];
		$this->user = (new LocalUser)->get($this->userId)[0];

		return $this;
	}
}
