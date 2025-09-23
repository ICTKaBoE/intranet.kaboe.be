<?php

namespace Database\Repository\Sync;

use Database\Interface\Repository;

class Action extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_sync_action", \Database\Object\Sync\Action::class, orderField: "name", deletedField: false, guidField: false);
    }
}
