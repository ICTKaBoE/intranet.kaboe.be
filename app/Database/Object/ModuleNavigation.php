<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\Module;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\Icon;

class ModuleNavigation extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"page",
		"name",
		"icon",
		"order",
		"deleted"
	];

	public function init()
	{
		$this->link = Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . "/{$this->module->module}/{$this->page}";
		$this->iconData = $this->icon ? Icon::load($this->icon) : false;

		$this->isActive = Strings::equal(Helpers::get_page(), $this->page);
	}

	public function link()
	{
		$this->module = (new Module)->get($this->moduleId, deleted: true)[0];
	}
}
