<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\Supplier;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class SupplierContact extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"supplierId",
		"name",
		"firstName",
		"email",
		"phone",
		"isMainContact",
		"deleted"
	];

	public function init()
	{
		$this->fullName = "{$this->firstName} {$this->name}";
		$this->isMainContactIcon = (Strings::equal($this->isMainContact, 1) ? "check" : "");
	}

	public function link()
	{
		$this->supplier = Arrays::firstOrNull((new Supplier)->get($this->supplierId));
	}
}
