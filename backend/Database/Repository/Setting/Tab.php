<?php

namespace Database\Repository\Setting;

use Database\Interface\Repository;

class Tab extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_setting_tab", \Database\Object\Setting\Tab::class, guidField: false);
    }
}
