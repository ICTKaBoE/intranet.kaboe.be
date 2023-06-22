<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\Mapping;
use Ouzo\Utilities\Strings;
use Security\Input;

class InformatStaff extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatUID",
		"masterNumber",
		"name",
		"firstName",
		"birthPlace",
		"birthDate",
		"sex",
		"insz",
		"diploma",
		"homePhone",
		"mobilePhone",
		"privateEmail",
		"schoolEmail",
		"addressStreet",
		"addressNumber",
		"addressBus",
		"addressZipcode",
		"addressCity",
		"addressCountry",
		"bankAccount",
		"bankId",
		"active",
		"deleted"
	];

	public function init()
	{
		$name = Input::clean($this->name);
		$firstName = Input::clean($this->firstName);

		$email = $firstName . "." . $name . "@" . EMAIL_SUFFIX;

		if (!Strings::equal($this->schoolEmail, $email)) $this->schoolEmail = str_replace(" ", "", strtolower($email));
		$this->addressCountryFull = Mapping::get("sync/informatStaff/country/{$this->addressCountry}");
		$this->addressHash = str_replace(" ", "", strtolower("{$this->addressStreet}{$this->addressNumber}{$this->addressBus}{$this->addressZipcode}{$this->addressCity}{$this->addressCountryFull}"));
	}
}