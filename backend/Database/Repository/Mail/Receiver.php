<?php

namespace Database\Repository\Mail;

use Database\Interface\Repository;

class Receiver extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_mail_receiver", \Database\Object\Mail\Receiver::class, orderField: 'email', guidField: false);
    }

    public function getByMailId($mailId)
    {
        $statement = $this->prepareSelect();
        $statement->where('mailId', $mailId);

        return $this->executeSelect($statement);
    }
}
