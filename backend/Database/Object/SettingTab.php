<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\Icon;

class SettingTab extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"name",
		"icon",
		"order",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];

	public function init()
	{
		$this->iconData = $this->icon ? Icon::load($this->icon) : "";
	}
}
