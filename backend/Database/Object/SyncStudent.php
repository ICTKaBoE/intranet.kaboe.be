<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\School;
use Database\Repository\SchoolInstitute;
use Ouzo\Utilities\Strings;
use Security\Input;

class SyncStudent extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"informatUID",
		"instituteId",
		"uid",
		"name",
		"firstName",
		"displayName",
		"email",
		"class",
		"description",
		"companyName",
		"memberOf",
		"samAccountName",
		"ou",
		"password",
		"action",
		"lastAdSyncSuccessAction",
		"lastAdSyncTime",
		"lastAdSyncError",
		"deleted"
	];

	public function init()
	{
		$this->_orderfield = "{$this->class}-{$this->name}-{$this->firstName}";
	}

	public function link()
	{
		$this->institute = (new SchoolInstitute)->get($this->instituteId)[0];
		$this->institute->link();
	}
}
