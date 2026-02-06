<?php

namespace Database\Repository\Mail;

use Database\Interface\Repository;

class Attachment extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_mail_attachment", \Database\Object\Mail\Attachment::class, orderField: 'name', guidField: false);
    }

    public function getByMailId($mailId)
    {
        $statement = $this->prepareSelect(filters: ['mailId' => $mailId]);
        return $this->executeSelect($statement);
    }
}
