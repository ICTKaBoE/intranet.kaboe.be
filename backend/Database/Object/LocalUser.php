<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\CString;
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
		"jobTitle",
		"companyName"
	];

	public function init()
	{
		$this->fullName = Strings::trimToNull($this->firstName . " " . $this->name);
		$this->fullNameReversed = Strings::trimToNull($this->name . " " . $this->firstName);
		$this->initials = CString::firstLetterOfEachWord((Strings::isNotBlank($this->firstName) ? $this->firstName . ' ' : '') . $this->name);

		$this->api = Input::convertToBool($this->api);
	}
}
