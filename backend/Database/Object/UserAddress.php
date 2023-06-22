<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\Icon;
use Ouzo\Utilities\Strings;

class UserAddress extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"userId",
		"street",
		"number",
		"bus",
		"zipcode",
		"city",
		"country",
		"current",
		"deleted"
	];

	public function init()
	{
		$this->formatted = $this->street . " " . $this->number . ($this->bus ? $this->bus : "") . ", " . $this->zipcode . " "  . $this->city . ", " . $this->country;
		$this->formattedCurrent = $this->formatted . (Strings::equal($this->current, 1) ? " (huidig)" : "");
		$this->currentIcon = (Strings::equal($this->current, 1) ? Icon::load("check") : "");
		$this->addressHash = str_replace(" ", "", strtolower("{$this->street}{$this->number}{$this->bus}{$this->zipcode}{$this->city}{$this->country}"));
	}
}
