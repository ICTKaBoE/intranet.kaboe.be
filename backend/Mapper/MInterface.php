<?php

namespace Mapper;

use stdClass;

class MInterface extends stdClass
{
	protected $mapFields = [];

	public function map($obj1, $obj2, $defaultValue = null)
	{
		$obj2 = $this->formatFields($obj2);

		foreach ($this->mapFields as $obj1Key => $obj2Key) {
			$obj1->$obj1Key = (isset($obj2->$obj2Key) ? $obj2->$obj2Key : $defaultValue);
		}

		return $obj1;
	}

	public function formatFields($obj2)
	{
		return $obj2;
	}
}
