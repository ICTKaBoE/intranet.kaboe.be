<?php

namespace Database\Repository\Violence;

use Database\Interface\Repository;

class Form extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_violence_form", \Database\Object\Violence\Form::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
