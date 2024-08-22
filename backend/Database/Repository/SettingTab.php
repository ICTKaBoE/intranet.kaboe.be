<?php

namespace Database\Repository;

use Database\Interface\Repository;

class SettingTab extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_setting_tab", \Database\Object\SettingTab::class);
    }
}
