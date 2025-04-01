<?php

namespace Database\Repository\Signage;

use Database\Interface\Repository;

class Media extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_signage_media", \Database\Object\Signage\Media::class, orderField: 'alias');
    }
}
