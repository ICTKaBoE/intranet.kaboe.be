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

	protected $userName = "";
	protected $userFirstName = "";

	public function init()
	{
		$this->userName = $this->user->name;
		$this->userFirstName = $this->user->firstName;
	}

	public function link()
	{
		$this->mainSchool = (new School)->get($this->mainSchoolId)[0];
		$this->user = (new LocalUser)->get($this->userId)[0];
		return $this;
	}
}
