<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\School;
use Database\Repository\UserAddress;
use Ouzo\Utilities\Strings;

class UserHomeWorkDistance extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"startAddressId",
		"endSchoolId",
		"distance",
		"alias",
		"color",
		"deleted"
	];

	protected $encodeAttributes = [
		"alias"
	];

	private $textColors = [
		"black" => ['azure', 'pink', 'orange', 'yellow', 'lime', 'cyan'],
		"white" => ['blue', 'indigo', 'purple', 'red', 'green', 'teal']
	];

	public function init()
	{
		$this->borderColor = $this->color;
		$this->textColor = "black";

		foreach ($this->textColors as $tc => $bcs) {
			foreach ($bcs as $bc) {
				if (Strings::equal($this->color, $bc)) {
					$this->textColor = $tc;
					break;
				}
			}
		}
	}

	public function link()
	{
		$this->startAddress = (new UserAddress)->get($this->startAddressId)[0];
		$this->endSchool = (new School)->get($this->endSchoolId)[0];
		return $this;
	}
}
