<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\Repository;

class SettingOverride extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_setting_override", \Database\Object\SettingOverride::class, orderField: false);
	}

	public function getBySettingAndModule($settingId, $moduleId)
	{
		$items = $this->get();
		$items = Arrays::filter($items, fn ($i) => Strings::equal($i->settingId, $settingId) && Strings::equal($i->moduleId, $moduleId));

		return Arrays::firstOrNull($items);
	}
}
