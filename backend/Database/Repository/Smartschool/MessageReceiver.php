<?php

namespace Database\Repository\Smartschool;

use Database\Interface\Repository;

class MessageReceiver extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_smartschool_message_receiver", \Database\Object\Smartschool\MessageReceiver::class, orderField: 'username', guidField: false);
    }

    public function getByMessageId($messageId)
    {
        $statement = $this->prepareSelect(filters: ['messageId' => $messageId]);
        return $this->executeSelect($statement);
    }
}
