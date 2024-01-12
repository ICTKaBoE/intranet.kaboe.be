<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Object\LocalUser as ObjectLocalUser;
use Database\Repository\LocalUser;
use Helpers\Mapping;
use Ouzo\Utilities\Clock;

class Log extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"type",
		"creationDateTime",
		"userId",
		"route",
		"description"
	];

	public function init()
	{
		$this->typeFull = Mapping::get("log/type/{$this->type}/description");
		$this->typeColor = Mapping::get("log/type/{$this->type}/color");

		$this->datetime = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
	}

	public function link()
	{
		$this->user = (new LocalUser)->get($this->userId)[0] ?? (new ObjectLocalUser([
			'id' => 0,
			'name' => 'Systeem',
			'firstName' => ''
		]));
	}
}
