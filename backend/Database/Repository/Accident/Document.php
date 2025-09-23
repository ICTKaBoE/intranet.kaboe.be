<?php

namespace Database\Repository\Accident;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Document extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_accident_document", \Database\Object\Accident\Document::class, orderField: "alias");
    }
}
