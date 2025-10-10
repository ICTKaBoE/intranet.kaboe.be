<?php

namespace Database\Repository\Violence;

use Database\Interface\Repository;

class Cause extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_violence_cause", \Database\Object\Violence\Cause::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
