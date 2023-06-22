<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Strings;
use Security\Input;

class InformatStudentExtra extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatUID",
		"instituteId",
		"name",
		"firstName",
		"nickname",
		"masterNumber",
		"bisNumber",
		"deleted"
	];

	public function init()
	{
		$name = Input::clean($this->name);
		$firstName = Input::clean($this->firstName);

		$email = $firstName . "." . $name . "@" . EMAIL_SUFFIX;

		if (!Strings::equal($this->schoolEmail, $email)) $this->schoolEmail = str_replace(" ", "", strtolower($email));
	}
}
