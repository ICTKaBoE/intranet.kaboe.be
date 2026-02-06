<?php

namespace Database\Repository\Smartschool;

use Database\Interface\Repository;
use Ouzo\Utilities\Clock;

class Message extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_smartschool_message", \Database\Object\Smartschool\Message::class, orderField: false, guidField: false);
    }
}
