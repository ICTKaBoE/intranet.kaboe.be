<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Security\Input;

class CheckStudentRelationInsz extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"school",
		"class",
		"childName",
		"childInsz",
		"motherName",
		"motherInsz",
		"fatherName",
		"fatherInsz",
		"insertDateTime",
		"locked",
		"published",
		"deleted"
	];

	public function init()
	{
		$this->childInsz = Input::formatInsz($this->childInsz);
		$this->motherInsz = Input::formatInsz($this->motherInsz);
		$this->fatherInsz = Input::formatInsz($this->fatherInsz);
	}
}
