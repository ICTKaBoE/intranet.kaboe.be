<?php

namespace Database\Repository\Setting;

use Database\Interface\Repository;

class Setting extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_setting", \Database\Object\Setting\Setting::class, guidField: false);
    }

    public function getBySettingTabId($settingTabId)
    {
        $statement = $this->prepareSelect();
        $statement->where('settingTabId', $settingTabId);

        return $this->executeSelect($statement);
    }
}
