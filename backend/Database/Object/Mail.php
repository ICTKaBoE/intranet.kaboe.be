<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Security\Input;

class Mail extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"subject",
		"body",
		"html",
		"receivers",
		"replyTo",
		"sendAt",
		"sent",
		"deleted"
	];

	public function init()
	{
		$this->html = Input::convertToBool($this->html);
		$this->sent = Input::convertToBool($this->sent);
	}

	public function link()
	{
		$this->receivers = json_decode($this->receivers, true);
		$this->replyTo = json_decode($this->replyTo, true);
	}
}
