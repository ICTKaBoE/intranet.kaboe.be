<?php

namespace Database\Object;

use Helpers\Mapping;
use Ouzo\Utilities\Clock;
use Database\Repository\School;
use Database\Repository\LocalUser;
use Database\Interface\CustomObject;
use Database\Repository\OrderSupplier;

class Order extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"creatorId",
		"acceptorId",
		"supplierId",
		"status",
		"description",
		"deleted"
	];

	public function init()
	{
		$this->number = "#" . str_pad($this->id, 6, 0, STR_PAD_LEFT);
		$this->statusFull = Mapping::get("order/status/{$this->status}/description");
		$this->statusColor = Mapping::get("order/status/{$this->status}/color");
	}

	public function link()
	{
		$localuserRepo = new LocalUser;

		$this->school = (new School)->get($this->schoolId)[0];
		$this->creator = $localuserRepo->get($this->creatorId)[0];
		$this->acceptor = $localuserRepo->get($this->acceptorId)[0];
		$this->supplier = (new OrderSupplier)->get($this->supplierId)[0];
	}
}
