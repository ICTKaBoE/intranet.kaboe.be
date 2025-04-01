<?php

namespace Database\Repository\Mail;

use Database\Interface\Repository;
use Ouzo\Utilities\Clock;

class Mail extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_mail", \Database\Object\Mail\Mail::class, orderField: false, guidField: false);
    }
}
