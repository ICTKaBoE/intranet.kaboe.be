<?php

namespace JAMF\Repository;

use JAMF\Interface\Repository;

class Device extends Repository
{
	public function __construct()
	{
		parent::__construct('devices', \JAMF\Object\Device::class);
	}
}
