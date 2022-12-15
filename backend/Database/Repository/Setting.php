<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Setting extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_setting", \Database\Object\Setting::class, orderField: false);
	}
}
