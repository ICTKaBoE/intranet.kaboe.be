<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Router\Helpers;
use Helpers\Icon;
use Ouzo\Utilities\Strings;

class Module extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"module",
		"name",
		"icon",
		"iconBackgroundColor",
		"scope",
		"order",
		"redirect",
		"assignUserRights",
		"defaultRights",
		"visible",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];

	public function init()
	{
		$this->icon = is_null($this->icon) ? false : $this->icon;

		$this->link = Strings::isBlank($this->redirect) ? (Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . Helpers::getPrefix() . "/{$this->module}") : $this->redirect;
		$this->target = Strings::isBlank($this->redirect) ? "_self" : "_blank";
		$this->iconData = $this->icon ? Icon::load($this->icon) : false;
	}
}
