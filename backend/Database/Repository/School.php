<?php

namespace Database\Repository;

use Database\Interface\Repository;

class School extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_school", \Database\Object\School::class, orderField: 'name');
	}
}
