<?php

namespace Database\Repository\General;

use Database\Interface\Repository;

class MessageType extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_general_message_type", \Database\Object\General\MessageType::class, orderField: "name", deletedField: false, guidField: false);
    }
}
