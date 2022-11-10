<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Strings;

class LocalUser extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"o365Id",
		"name",
		"firstName",
		"username",
		"password",
		"jobTitle",
		"companyName",
		"active",
		"deleted"
	];

	public function init()
	{
		$this->fullName = $this->firstName . " " . $this->name;
		$this->initials = (Strings::isNotBlank($this->firstName) ? substr($this->firstName, 0, 1) : '') . substr($this->name, 0, 1);
	}
}
