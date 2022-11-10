<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Router\Helpers;
use Security\Icon;

class Module extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"module",
		"name",
		"icon",
		"iconBackgroundColor",
		"order",
		"deleted"
	];

	public function init()
	{
		$this->icon = is_null($this->icon) ? false : $this->icon;

		$this->link = Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . "/{$this->module}";
		$this->iconData = $this->icon ? Icon::load($this->icon) : false;
	}
}
