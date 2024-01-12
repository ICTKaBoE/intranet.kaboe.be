<?php

namespace Database\Object;

use Helpers\Mapping;
use Ouzo\Utilities\Clock;
use Database\Repository\School;
use Database\Interface\CustomObject;

class Library extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"isdn",
		"author",
		"title",
		"schoolId",
		"numberOfCopies",
		"category",
		"lastActionDateTime",
		"numberOfAvailableCopies",
		"deleted"
	];

	public function init()
	{
		$this->available = ($this->numberOfAvailableCopies == 0 ? "x" : "check");
		$this->lastAction = Clock::at($this->lastActionDateTime)->format("d/m/Y H:i:s");
		$this->categoryFull = Mapping::get("schoollibrary/category/{$this->category}");
		$this->lendTitle = "{$this->author} - {$this->title} ({$this->categoryFull})";
		$this->_orderfield = "{$this->schoolId}-{$this->category}-{$this->title}";
    }

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
	}
}
