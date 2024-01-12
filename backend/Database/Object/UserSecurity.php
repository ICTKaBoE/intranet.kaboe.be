<?php

namespace Database\Object;

use Database\Repository\Module;
use Database\Repository\LocalUser;
use Database\Interface\CustomObject;
use Helpers\Icon;
use Ouzo\Utilities\Strings;
use Security\Input;

class UserSecurity extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"moduleId",
		"userId",
		"view",
		"edit",
		"export",
		"changeSettings",
		"locked",
		"deleted",
	];

	public function init()
	{
		$this->view = Input::convertToBool($this->view);
		$this->edit = Input::convertToBool($this->edit);
		$this->export = Input::convertToBool($this->export);
		$this->changeSettings = Input::convertToBool($this->changeSettings);
		$this->locked = Input::convertToBool($this->locked);
	}

	public function link()
	{
		$this->module = (new Module)->get($this->moduleId)[0];
		$this->user = (new LocalUser)->get($this->userId)[0];

		$this->tableOrder = $this->user->fullName;
		return $this;
	}
}
