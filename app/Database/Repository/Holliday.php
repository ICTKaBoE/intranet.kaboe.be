<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Holliday extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_holliday", \Database\Object\Holliday::class, orderField: 'start');
	}
}
