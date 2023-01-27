<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Strings;
use Security\Input;

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
		"api",
		"active",
		"deleted"
	];

	protected $encodeAttributes = [
		"name",
		"firstName",
		"jobTitle",
		"companyName"
	];

	public function init()
	{
		$this->fullName = Strings::trimToNull($this->firstName . " " . $this->name);
		$this->fullNameReversed = Strings::trimToNull($this->name . " " . $this->firstName);
		$this->initials = Strings::trimToNull((Strings::isNotBlank($this->firstName) ? substr($this->firstName, 0, 1) : '') . substr($this->name, 0, 1));

		$this->api = Input::convertToBool($this->api);
	}
}
