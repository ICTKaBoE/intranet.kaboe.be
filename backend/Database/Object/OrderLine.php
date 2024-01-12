<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\ManagementBeamer;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementPrinter;
use Database\Repository\Order;
use Helpers\CString;
use Helpers\Icon;
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
		"warenty",
		"accepted",
		"deleted"
	];

	public function init()
	{
		$this->forDescription = Mapping::get("order/line/for/{$this->for}");
		$this->quotationVatIncluded = Input::convertToBool($this->quotationVatIncluded);
		$this->warenty = Input::convertToBool($this->warenty);
		$this->accepted = Input::convertToBool($this->accepted);

		$this->quotationVatIncludedText = ($this->warenty ? "Garantiewissel" : CString::formatCurrency($this->quotationPrice) . ($this->quotationVatIncluded ? " (incl. btw)" : ""));
	}

	public function link($noParent = false)
	{
		if (!$noParent) $this->order = (new Order)->get($this->id)[0];
		$this->asset = null;

		if (Strings::equal($this->for, "L") || Strings::equal($this->for, "D")) $this->asset = (new ManagementComputer)->get($this->assetId)[0];
		else if (Strings::equal($this->for, "B")) $this->asset = (new ManagementBeamer)->get($this->assetId)[0];
		else if (Strings::equal($this->for, "P")) $this->asset = (new ManagementPrinter)->get($this->assetId)[0];
	}
}
