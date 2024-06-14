<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\SchoolInstitute;

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
		'active',
		"deleted"
	];

	public function init()
	{
		$this->displayNameReversed = "{$this->name} {$this->firstName}";
		$this->_orderfield = "{$this->class}-{$this->name}-{$this->firstName}";
	}

	public function link()
	{
		$this->institute = (new SchoolInstitute)->get($this->instituteId)[0];
		$this->institute->link();
	}
}
