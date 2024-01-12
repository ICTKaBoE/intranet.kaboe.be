<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\SupplierContact;

class Supplier extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"name",
		"email",
		"phone",
		"street",
		"number",
		"bus",
		"zipcode",
		"city",
		"country",
		"deleted"
	];

	public function init()
	{
		$this->formattedAddress = $this->street . " " . $this->number . ($this->bus ? $this->bus : "") . ", " . $this->zipcode . " "  . $this->city . ", " . $this->country;
		$this->nameWithMainContact = "{$this->name} ({$this->mainContact->fullName})";
	}

	public function link()
	{
		$this->mainContact = (new SupplierContact)->getMainContactBySupplierId($this->id);

		return $this;
	}
}
