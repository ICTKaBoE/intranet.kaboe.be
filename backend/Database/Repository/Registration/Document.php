<?php

namespace Database\Repository\Registration;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Document extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_registration_document", \Database\Object\Registration\Document::class, orderField: "alias");
    }

    public function getByType($type)
    {
        $statement = $this->prepareSelect(filters: ['type' => $type]);
        return $this->executeSelect($statement);
    }
}
