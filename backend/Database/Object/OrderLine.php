<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\ManagementBeamer;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementPrinter;
use Database\Repository\Order;
use Helpers\Mapping;
use Ouzo\Utilities\Strings;
use Security\Input;

class OrderLine extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"orderId",
		"for",
		"assetId",
		"what",
		"reason",
		"amount",
		"quotationPrice",
		"quotationVatIncluded",
		"accepted",
		"deleted"
	];

	public function init()
	{
		$this->forDescription = Mapping::get("order/line/for/{$this->for}");
		$this->accepted = Input::convertToBool($this->accepted);
	}

	public function link()
	{
		$this->order = (new Order)->get($this->id)[0];
		$this->asset = null;

		if (Strings::equal($this->for, "L") || Strings::equal($this->for, "D")) $this->asset = (new ManagementComputer)->get($this->assetId)[0];
		else if (Strings::equal($this->for, "B")) $this->asset = (new ManagementBeamer)->get($this->assetId)[0];
		else if (Strings::equal($this->for, "P")) $this->asset = (new ManagementPrinter)->get($this->assetId)[0];
	}
}
