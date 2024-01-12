<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\Module;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Helpers\Icon;

class ModuleNavigation extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"page",
		"name",
		"icon",
		"minimumRights",
		"order",
		"deleted"
	];

	protected $encodeAttributes = [
		"name"
	];

	public function init()
	{
		$this->link();

		$this->link = Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . "/" . Helpers::getPrefix() . "/{$this->module->module}/{$this->page}";
		$this->isActive = (Strings::equal(Helpers::getPage(), $this->page) ? 'active' : '');
	}

	public function link()
	{
		$this->module = (new Module)->get($this->moduleId, deleted: true)[0];
		return $this;
	}
}
